<?php
if (!defined('IN_UOKE')) exit();
/**
 * Class Core
 * 
 * @desc Core class is Uoke core,
 * AutoLoad , Exception inside
 */
class Core {
    private static $fileCache = array();
    
    public function __construct() {}
    
    public static function start() {
        error_reporting(0);
        spl_autoload_register('Core::autoload');
        register_shutdown_function('Core::appSystemError');
        set_error_handler('Core::errorException');
        set_exception_handler('Core::errorException');
        require SYSTEM_PATH.'/Function/core.php';
        define('CHARSET', CONFIG('charset'));
        ini_set('date.timezone', CONFIG('timezone'));
        header('Content-Type: text/html; charset=' . CHARSET);
        header('X-XSS-Protection: 0');
        UOKE_DEBUG && Helper\Log::runLog();
    }
    
    public static function autoLoad($className) {
        $classNameExplode = explode('\\', $className);
        if(!class_exists($className) && isset(self::$fileCache[$className]) == false) {
            $corePath = array('helper', 'adapter', 'factory', 'action', 'services');
            $fileClass = self::smartFileFound($classNameExplode, $corePath, $className);
            if(file_exists_case($fileClass.'.php')) {
                self::autoLoadFileCache($className, $fileClass.'.php');
            } else {
                throw new Exception($className.' not found, Get File: '.$fileClass.'.php');
            }
        }
    }

    private static function smartFileFound($classNameExplode, $corePath, $className) {
        if((MAIN_PATH !== SYSTEM_PATH) && in_array(strtolower($classNameExplode[1]), array('action', 'services'))) {
            unset($classNameExplode[0]);
            $classFile = str_replace('_', '/', implode('/',$classNameExplode));
            $fileClass = MAIN_PATH.$classFile;
        } else {
            if(in_array(strtolower($classNameExplode[0]), $corePath)) {
                $classFile = str_replace('_', '/', implode('/',$classNameExplode));
                $fileClass = SYSTEM_PATH.$classFile;
            } else {
                $classFile = str_replace('\\', '/', $className);
                $fileClass = SYSTEM_PATH.'Extend/'.$classFile;
            }
        }
        return $fileClass;
    }
    /*
     * @param $class, $classFile
     */
    private static function autoLoadFileCache(string $class, string $classFile) {
        if(empty(self::$fileCache[$class]) && $classFile) {
            include($classFile);
            self::$fileCache[$class] = $classFile;
        }
    }
    
    public static function errorException($e, $errstr = '', $errfile = '', $errline = '') {
        if(!in_array($e, array(E_NOTICE, E_WARNING))) {
            new Uoke\uError($e, $errstr, $errfile, $errline);
        }
    }
    
    public static function appSystemError() {
        if ($e = error_get_last()) {
            new Uoke\uError($e);
        }
        UOKE_DEBUG && Helper\Log::saveLog();
    }
}