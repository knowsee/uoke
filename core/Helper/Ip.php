<?php
namespace Helper;

class Ip {
	
	public static function mask2piece($sourceIp, $lastIp, $toMask = '29', $checkWay = true) {
		$beginIp = toLong($sourceIp);
		$lastIp = toLong($lastIp);
		$needHandle = $ipList = [];
		$num = 0;
		for($i=$beginIp; $i<=$lastIp; $i++) {
			$num++;
			if($num == 2) {
				$needHandle[] = $i;
			}
			if($num == 3 && $checkWay == false) {
				$needHandle[] = $i;
			}
			if($num == self::getMask($toMask)) {
				$num = 0;
			}
		}
		return self::handleLong($needHandle);
	}
	
	public static function listIp($sourceIp, $num = 5) {
		$beginIp = toLong($sourceIp);
		$ipList = [];
		$ipList[] = $beginIp;
		for($i=1; $i<$num; $i++) {
			$ipList[] = $beginIp+$i;
		}
		return self::handleLong($ipList);
	}
	
	private static function handleLong(array $list) {
		foreach($list as $ip) {
			$ipList[] = long2ip($ip);
		}
		return $ipList;
	}
	
	private static function getMask($ipMask) {
		return (pow(2, (32 - $ipMask)));
	}

}