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

    public static function openDebug($levels) {
        self::$debug = $levels;
    }

    public static function createOrder($orderId, $orderInfo = array()) {
        if(self::$debug !== self::DEBUG_TO_DIS) {
            self::openLink();
        }
        $orderInfo = self::send($orderId, self::handleInfo($orderInfo));
        if(self::$debug !== self::DEBUG_TO_DIS) {
            self::$link->close();
        }
        if(self::$debug == self::DEBUG_TO_LOG) {
            Log::writeOtherLogFile('HongLing_Log', self::$xml);
        }
        return $orderInfo;
    }

    public static function getXml() {
        return self::$xml;
    }

    public static function makeID() {
        return uniqid(date('mds'));
    }

    private static function handleInfo($orderInfo) {
        $orderInfo['custormerStyle'] = implode(',', json_decode($orderInfo['custormerStyle']));
        $orderInfo['styleInfo'] = self::keyNvalue(json_decode($orderInfo['styleInfo']));
        $orderInfo['stylePartsize'] = self::keyNvalue(json_decode($orderInfo['stylePartsize']));
        return $orderInfo;
    }

    private static function keyNvalue(array $array, string $p = ':') {
        if(!$array) return ;
        foreach($array as $key => $value) {
            if($key) {
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

    private static function send($orderId, $orderInfo) {
        $xml = self::loadXml();
        self::$xml = self::xmlMake($xml, $orderId, $orderInfo);
        error_log(self::$xml);
        if(self::$debug !== self::DEBUG_TO_DIS) {
            $return = self::$link->post(
                self::$debug == self::DEBUG_CLOSE ? self::REST_ORDER_SUBMIT_URL : self::REST_ORDER_SUBMIT_DEBUG_URL,
                self::$xml
            );
            if($return->code == 101) {
                return array('orderPass' => true, 'orderId' => $return->rs, 'orderJh' => $return->jhrq);
            } else {
                return array('orderPass' => false, 'orderMsg' => $return->rs);
            }
        } else {
            return array(
                'code' => 102,
                'rs' => null,
                'jhrq' => 0
            );
        }
    }

    private static function xmlMake($xml, $orderId, $orderInfo) {
        $base = array(
            'clothingid' => $orderInfo['orderCid'], //EG
            'sizecategoryid' => 10052,
            'areaid' => null,
            'fabric' => $orderInfo['clothFabric'], //EG
            'amount' => $orderInfo['orderNum'],
            'clothingstyle' => $orderInfo['clothStyle'], //EG
            'custormerbody' => $orderInfo['custormeiStyle'],
            'orderno' => $orderId,
            'semifinished' => 2,
            'remark' => ''
        );
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
        $orderDetail = array(
            'sizespecheight' => null,
            'categoryid' => $orderInfo['styleCid'], //EG
            'bodystyle' => $orderInfo['styleHow'], //EG
            'partsize' => $orderInfo['styleInfo'],
            'ordersprocess' => $orderInfo['stylePartsize']
        );
        $oDsXml = $xml->addChild('orderdetails');
        $oDXml = $oDsXml->addChild('orderdetail');
        self::setXml($orderDetail, $oDXml);
        if($orderInfo['styleNeedEmb']) {
            $ep = $oDXml->addChild('embroideryprocess');
            $ebd = $ep->addChild('embroidery');
            $embroideryProcess = array(
                'position'  => $orderInfo['embPosition'],
                'font'  => $orderInfo['embFont'],
                'color'  => $orderInfo['embColor'],
                'content'  => $orderInfo['embContent'],
                'fontsize'  => $orderInfo['embFontsize']
            );
            self::setXml($embroideryProcess, $ebd);
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