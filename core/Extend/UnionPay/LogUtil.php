<?php
namespace UnionPay;
class LogUtil
{
	private static $_logger = null;
	public static function getLogger()
	{
		if (LogUtil::$_logger == null ) {
			LogUtil::$_logger = new PhpLog ( SDKConfig::SDK_LOG_FILE_PATH, "PRC", SDKConfig::SDK_LOG_LEVEL );
		}
		return self::$_logger;
	}
}

