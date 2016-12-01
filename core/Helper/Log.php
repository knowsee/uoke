<?php
namespace Helper;
class Log {
    
    const IMPORTANT = '9999';
    const NOTICE = '999';
    const INFO = '99';
    
    public static $_logMessage = array();
    public static $_logWriteObj = array();
    
    public static function runLog() {
        self::$_logWriteObj = array('begin' => UNIXTIME);
    }

    public static function writeLog($message, string $type = 'php', string $level = self::NOTICE) {
        self::$_logMessage[$type][UNIXTIME][$level][] = $message;
    }

    public static function writeOtherLogFile(string $name, string $message) {
        $msg = date('Y-m-d H:i:s', UNIXTIME)."\r\n\r\n";
        $msg .= $message;
        File::writeFile($msg,
            $name.'.txt',
            'Data/log/',
            array('append' => true));
    }
    
    public static function saveLog() {
        self::$_logWriteObj['end'] = time();
        self::$_logWriteObj['runtime'] = self::$_logWriteObj['end'] - self::$_logWriteObj['begin'];
        File::writeFile('====' . date('Y-m-d H:i:s', UNIXTIME) . '===' . "\r\n" . var_export(self::$_logWriteObj, TRUE). "\r\n". var_export(self::$_logMessage, TRUE) . "\r\n\r\n",
            date('H') .'.txt',
            'Data/log/'.date('Y_m_d').'/',
            array('append' => true));
    }
    
}
