<?php
namespace Config\TraitConfig;
trait HttpCode {
	
	public function getCode($code) {
		
		$codeText = [
			404 => '���͵������޷��ҵ���Ӧ��Ӧ�ô���',
			400 => '���͵����󲻷�����ع����޷�����',
			403 => '�����ҳ��û�л����ɣ��뷵��',
			405 => '����ķ�ʽ����ȷ',
			500 => 'ϵͳ����',
		];
		
		return $codeText[$code];
	}
	
	
	public function errorGo($code) {
		return true;
	}
}