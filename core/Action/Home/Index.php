<?php
namespace Action;
use UnionPay\AcpService;
use UnionPay\SDKConfig;
class Home_Index {

    public function Index() {
        header ( 'Content-type:text/html;charset=utf-8' );
        $accNo = '6226388000000095';
        $customerInfo = array(
            'certifTp' => '01',
            'certifId' => '510265790128303',
            'customerNm' => '张三',
        );

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
            'merId' => '777290058110097',		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => date('YmdHis'),	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => date('YmdHis'),	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => 1000,	//交易金额，单位分，此处默认取demo演示页面传递的参数
// 		'reqReserved' =>'透传信息',        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据

// 		'accNo' => $accNo,     //卡号，旧规范请按此方式填写
// 		'customerInfo' => com\unionpay\acp\sdk\AcpService::getCustomerInfo($customerInfo), //持卡人身份信息，旧规范请按此方式填写
            'accNo' =>  AcpService::encryptData($accNo),     //卡号，新规范请按此方式填写
            'customerInfo' => AcpService::getCustomerInfoWithEncrypt($customerInfo), //持卡人身份信息，新规范请按此方式填写
        );

        AcpService::sign ( $params ); // 签名
        $url = SDKConfig::SDK_BACK_TRANS_URL;

        $result_arr = AcpService::post ( $params, $url);
        if(count($result_arr)<=0) { //没收到200应答的情况
            $this->printResult ( $url, $params, "" );
            return;
        }

        $this->printResult ($url, $params, $result_arr ); //页面打印请求应答数据

        if (!AcpService::validate ($result_arr) ){
            echo "应答报文验签失败<br>\n";
            return;
        }

        echo "应答报文验签成功<br>\n";
        if ($result_arr["respCode"] == "00"){
            //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
            //TODO
            echo "受理成功。<br>\n";
        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05"
            || $result_arr["respCode"] == "01"
            || $result_arr["respCode"] == "12"
            || $result_arr["respCode"] == "34"
            || $result_arr["respCode"] == "60" ){
            //后续需发起交易状态查询交易确定交易状态
            //TODO
            echo "处理超时，请稍后查询。<br>\n";
        } else {
            //其他应答码做以失败处理
            //TODO
            echo "失败：" . $result_arr["respMsg"] . "。<br>\n";
        }
    }

    private function printResult($url, $req, $resp) {
        echo "=============<br>\n";
        echo "地址：" . $url . "<br>\n";
        echo "请求：" . str_replace ( "\n", "\n<br>", htmlentities ( createLinkString ( $req, false, true ) ) ) . "<br>\n";
        echo "应答：" . str_replace ( "\n", "\n<br>", htmlentities ( createLinkString ( $resp , false, true )) ) . "<br>\n";
        echo "=============<br>\n";
    }

}