<?php
namespace Helper;
class Log {
	
    public static $_logMessage = array();
    public static $_logWriteObj = array();
    
    public static function runLog() {
        self::$_logWriteObj = array('begin' => microtimeSum());
    }
    
    public static function viewLog() {
        self::$_logWriteObj['end'] = microtimeSum();
        self::$_logWriteObj['runtime'] = self::$_logWriteObj['end'] - self::$_logWriteObj['begin'];
        return self::$_logWriteObj;
    }

    public static function writeLog($message, $type = 'php') {
        self::$_logMessage[$type][UNIXTIME][] = $message;
    }

    public static function writeOtherLogFile(string $name, string $message) {
        $msg = date('Y-m-d H:i:s', UNIXTIME)."\r\n\r\n";
        $msg .= $message;
        File::writeFile($msg,
            $name.'.txt',
            'Data/Log/',
            array('append' => true));
    }
    
    public static function saveLog() {
        self::$_logWriteObj['end'] = microtimeSum();
        self::$_logWriteObj['runtime'] = self::$_logWriteObj['end'] - self::$_logWriteObj['begin'];
        File::writeFile('====' . date('Y-m-d H:i:s', UNIXTIME) . '===' . "\r\n" . var_export(self::$_logWriteObj, TRUE). "\r\n". var_export(self::$_logMessage, TRUE) . "\r\n\r\n",
            date('H') .'.txt',
            'Data/Log/'.date('Y/m/d').'/',
            array('append' => true));
		self::$_logWriteObj = array();
    }
    
}
