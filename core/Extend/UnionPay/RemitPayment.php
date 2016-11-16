<?php
namespace UnionPay;
use MongoDB\BSON\Timestamp;
use Uoke\uError;

class RemitPayment {

    private static $cardInfo;
    private static $merId = '';

    public static function payTo($cardId, $Id, $realName, $money) {
        self::setInfo($cardId, $Id, $realName);
        $orderId = self::makeOrder();
        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',		      //版本号
            'encoding' => 'utf-8',		      //编码方式
            'signMethod' => '01',		      //签名方法
            'txnType' => '12',		          //交易类型
            'txnSubType' => '00',		      //交易子类
            'bizType' => '000401',		      //业务类型
            'accessType' => '0',		      //接入类型
            'channelType' => '08',		      //渠道类型
            'currencyCode' => '156',          //交易币种，境内商户勿改
            'backUrl' => SDKConfig::SDK_BACK_NOTIFY_URL, //后台通知地址
            'encryptCertId' => AcpService::getEncryptCertId(), //验签证书序列号

            //TODO 以下信息需要填写
            'merId' => self::$merId,		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $orderId,	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => date('YmdHis'),	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $money*100,	//交易金额，单位分，此处默认取demo演示页面传递的参数
            'accNo' =>  AcpService::encryptData(self::$cardInfo['accNo']),     //卡号，新规范请按此方式填写
            'customerInfo' => AcpService::getCustomerInfoWithEncrypt(self::$cardInfo['userInfo']), //持卡人身份信息，新规范请按此方式填写
        );
        AcpService::sign ($params);
        $url = SDKConfig::SDK_BACK_TRANS_URL;

        $result_arr = AcpService::post ($params, $url);
        if(count($result_arr)<=0) {
            return false;
        }
        if (!AcpService::validate ($result_arr) ){
            throw new uError('应答报文验签失败');
            return false;
        }
        if ($result_arr["respCode"] == "00"){
            return array('orderId' => $orderId);
        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05"
            || $result_arr["respCode"] == "01"
            || $result_arr["respCode"] == "12"
            || $result_arr["respCode"] == "34"
            || $result_arr["respCode"] == "60" ){
            return array('orderId' => $orderId);
        } else {
            return array('orderId' => $orderId, 'errorMsg' => $result_arr["respMsg"]);
        }
    }

    private static function makeOrder() {
        return mt_rand(time(), time()+10000).uniqid('Upay'.time());
    }

    private static function setInfo($accNo, $Id, $realName) {
        self::$cardInfo['accNo'] = $accNo;
        self::$cardInfo['userInfo'] = array(
            'certifTp' => '01',
            'certifId' => $Id,
            'customerNm' => $realName,
        );

    }
}