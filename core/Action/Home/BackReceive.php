<?php
namespace Action;
use UnionPay\AcpService;

class Home_BackReceive {

	public function Index() {
		if (isset ( $_POST ['signature'] )) {
			if(AcpService::validate ( $_POST )) {
				$orderId = $_POST ['orderId']; //其他字段也可用类似方式获取
				$respCode = $_POST ['respCode']; //判断respCode=00或A6即可认为交易成功
				echo 'true';
			} else {
				echo 'error';
			}
		} else {
			echo '签名为空';
		}
	}
}