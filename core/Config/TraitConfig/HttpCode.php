<?php
namespace Config\TraitConfig;
trait HttpCode {
	
	public function getCode($code) {
		
		$codeText = [
			404 => '发送的请求无法找到对应的应用处理',
			400 => '发送的请求不符合相关规则，无法处理',
			403 => '请求的页面没有获得许可，请返回',
			405 => '请求的方式不正确',
			500 => '系统错误',
		];
		
		return $codeText[$code];
	}
	
	
	public function errorGo($code) {
		return true;
	}
}