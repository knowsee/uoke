<?php
namespace HongLing;
use Curl\Curl,Helper\Json,Helper\Log;

class Order {

    /**
     * @var Curl
     */
    private static $link;
    private static $xml;
    private static $debug = 0;

    const DEBUG_TO_CLIENT = 3;
    const DEBUG_TO_LOG = 2;
    const DEBUG_TO_DIS = 1;
    const DEBUG_CLOSE = 0;

    const REST_USER = 'CKWX';
    const REST_PASS = '4008756651';
    const REST_ACCEPT = 'application/json';
    const REST_LAN = 'zh';

    const REST_ORDER_SUBMIT_URL = 'http://api.rcmtm.cn/api/order/submit';
    const REST_ORDER_SUBMIT_DEBUG_URL = 'http://api.rcmtm.cn/api/order';
    const REST_ORDER_QUERY = 'http://api.rcmtm.cn/api/order/status/';
    const REST_MIANLIAO_QUERY = 'http://api.rcmtm.cn/api/fabric/stock/';

    public static function openDebug($levels) {
        self::$debug = $levels;
    }

    public static function createOrder($orderInfo = array()) {
        if(self::$debug !== self::DEBUG_TO_DIS) {
            self::openLink();
        }
        $orderInfo = self::send(self::handleInfo($orderInfo));
        if(self::$debug !== self::DEBUG_TO_DIS) {
            self::$link->close();
        }
        if(self::$debug == self::DEBUG_TO_LOG) {
            Log::writeOtherLogFile('HongLing_Log', self::$xml);
        }
        return $orderInfo;
    }

    public static function queryOrder($orderId) {
        self::openLink();
        $info = self::query($orderId);
        self::$link->close();
        if($info->code == 10033) {
            return array('kuaidi' => $info->jhrq);
        } else {
            return array('kuaidi' => null);
        }
    }

    public static function queryFabric($fabric) {
        self::openLink();
        $info = self::queryMianLiao($fabric);
        self::$link->close();
        if($info->code == 101) {
            return (array)$info->rs;
        } else {
            return array();
        }
    }

    public static function getXml() {
        return self::$xml;
    }

    public static function makeID() {
        return date('mdhi').mt_rand(100,900);
    }

    private static function handleInfo($orderInfo) {
        $orderInfo['custormerStyle'] = implode(',', Json::decode($orderInfo['custormerStyle']));
        foreach($orderInfo['detailInfo'] as $k => $info) {
            $orderInfo['detailInfo'][$k]['styleInfo'] = self::keyNvalue(Json::decode($info['styleInfo']));
            $orderInfo['detailInfo'][$k]['stylePartsize'] = self::keyNvalue(Json::decode($info['stylePartsize']));
        }
        return $orderInfo;
    }

    private static function keyNvalue(array $array, string $p = ':') {
        if(!$array) return ;
        foreach($array as $key => $value) {
            if($key && $key > 20) {
                $a[] = $key.$p.$value;
            } else {
                $a[] = $value;
            }
        }
        return implode(',', $a);
    }

    private static function openLink() {
        self::$link = new Curl();
        self::$link->setHeaders(
            array(
                'user' => self::REST_USER,
                'pwd' => md5(self::REST_PASS),
                'accept' => self::REST_ACCEPT,
                'lan' => self::REST_LAN,
                'Content-Type' => 'text/xml',
                'charset' => 'utf-8'
            )
        );
    }

    private static function query($orderId) {
        $return = self::$link->get(
            self::REST_ORDER_QUERY.$orderId
        );
        return $return;
    }

    private static function queryMianLiao($fabric) {
        $return = self::$link->get(
            self::REST_MIANLIAO_QUERY.$fabric
        );
        return $return;
    }

    private static function send($orderInfo) {
        $xml = self::loadXml();
        Log::writeOtherLogFile('HongLing_Log_POST_Info', var_export($orderInfo, true));
        self::$xml = self::xmlMake($xml, $orderInfo);
        if(self::$debug !== self::DEBUG_TO_DIS) {
            $return = self::$link->post(
                self::$debug == self::DEBUG_CLOSE ? self::REST_ORDER_SUBMIT_URL : self::REST_ORDER_SUBMIT_DEBUG_URL,
                self::$xml
            );
            Log::writeOtherLogFile('HongLing_Log_Info', var_export($return, true));
            if($return->code == 101) {
                return array('orderPass' => true, 'orderId' => $return->rs, 'orderJh' => $return->jhrq);
            } else {
                return array('orderPass' => false, 'orderMsg' => $return->rs);
            }
        } else {
            return array(
                'orderPass' => false,
                'code' => 102,
                'rs' => null,
                'jhrq' => 0
            );
        }
    }

    private static function xmlMake($xml, $orderInfo) {
        $base = array(
            'clothingid' => $orderInfo['orderCid'], //EG
            'sizecategoryid' => 10052,
            'areaid' => null,
            'fabric' => $orderInfo['clothFabric'], //EG
            'amount' => $orderInfo['orderNum'],
            'clothingstyle' => $orderInfo['clothStyle'], //EG
            'custormerbody' => $orderInfo['custormerStyle'],

            'semifinished' => 2,
            'remark' => 'make order. '
        );
        //'orderno' => $orderId,
        self::setXml($base, $xml);
        $customerInformation = array(
            'name' => $orderInfo['clientName'],
            'height' => $orderInfo['clientHeight'],
            'heightunitid' => 10266,
            'weight' => $orderInfo['clientUnitid'],
            'weightunitid' => 10261,
            'email' => null,
            'address' => null,
            'tel' => $orderInfo['clientMobile'],
            'memos' => null
        );
        $cIXml = $xml->addChild('customerInformation');
        $cIXml->addAttribute('genderid', 10040); //EG
        self::setXml($customerInformation, $cIXml);

        $oDsXml = $xml->addChild('orderdetails');

        foreach($orderInfo['detailInfo'] as $k => $info) {
            $orderDetail = array(
                'sizespecheight' => null,
                'categoryid' => $info['styleCid'], //EG
                'bodystyle' => $info['styleHow'], //EG
                'partsize' => $info['styleInfo'],
                'ordersprocess' => $info['stylePartsize']
            );
            $oDXml = $oDsXml->addChild('orderdetail');
            self::setXml($orderDetail, $oDXml);
            if($info['styleNeedEmb']) {
                $ep = $oDXml->addChild('embroideryprocess');
                $ebd = $ep->addChild('embroidery');
                $embroideryProcess = array(
                    'position'  => $info['embPosition'],
                    'font'  => $info['embFont'],
                    'color'  => $info['embColor'],
                    'content'  => $info['embContent'],
                    'fontsize'  => $info['embSize']
                );
                self::setXml($embroideryProcess, $ebd);
            }
        }

        return $xml->asXML();
    }

    private static function setXml($Info, &$xml) {
        foreach($Info as $k => $v) {
            if(is_array($v)) {
                $childXml = $xml->addChild($k, $v);
                self::setXml($v, $childXml);
            } else {
                $xml->addChild($k, $v);
            }
        }

    }

    private static function loadXml() {
        $string = <<<XML
<?xml version='1.0' encoding='utf-8' standalone="yes" ?>
<orderinformation>
</orderinformation>
XML;
        return simplexml_load_string($string);
    }

}