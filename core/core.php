<?php

if (!defined('IN_UOKE'))
    exit();

/**
 * Class Core
 * 
 * @desc Core class is Uoke core,
 * AutoLoad , Exception inside
 */
class Core {

    private static $fileCache = array();

    public static function start() {
        error_reporting(0);
        spl_autoload_register('Core::autoload', true, false);
        register_shutdown_function('Core::appSystemError');
        set_error_handler('Core::errorException');
        set_exception_handler('Core::errorException');
        require SYSTEM_PATH . DIRECTORY_SEPARATOR . 'Function'. DIRECTORY_SEPARATOR .'core.php';
        if (extension_loaded('zlib')) {
            ob_start('ob_gzhandler');
        }
        Helper\Log::runLog();
    }

    public static function autoLoad($className) {
        $classNameExplode = explode('\\', $className);
        if (!class_exists($className) && isset(self::$fileCache[$className]) == false) {
            $corePath = array('helper', 'adapter', 'factory', 'action', 'services', 'config', 'smart');
            $fileClass = self::smartFileFound($classNameExplode, $corePath, $className);
            if (file_exists_case($fileClass . '.php')) {
                self::autoLoadFileCache($className, $fileClass . '.php');
            } else {
                throw new \Uoke\uError($className . ' Class Not Found');
            }
        }
    }

    private static function smartFileFound($classNameExplode, $corePath, $className) {
        if ((MAIN_PATH !== SYSTEM_PATH) && in_array(strtolower($classNameExplode[1]), array('action', 'services', 'smart'))) {
            $classFile = str_replace('_', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $classNameExplode));
            $fileClass = dirname(MAIN_PATH) . DIRECTORY_SEPARATOR . $classFile;
        } else {
            if (in_array(strtolower($classNameExplode[0]), $corePath)) {
                $classFile = str_replace('_', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $classNameExplode));
                $fileClass = SYSTEM_PATH . $classFile;
            } else {
                $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $className);
                $fileClass = SYSTEM_PATH . 'Extend' . DIRECTORY_SEPARATOR . $classFile;
            }
        }
        return $fileClass;
    }

    /*
     * @param $class, $classFile
     */

    private static function autoLoadFileCache(string $class, string $classFile) {
        if (empty(self::$fileCache[$class]) && $classFile) {
            include($classFile);
            self::$fileCache[$class] = $classFile;
        }
    }

    public static function errorException($e, $errstr = '', $errfile = '', $errline = '') {
        if (is_object($e)) {
            echo (new Uoke\uError($e, E_ERROR))->show();
        }
        if (!in_array($e, array(E_NOTICE, E_WARNING))) {
            $e = new \Uoke\uError($e, $errstr, $errfile, $errline);
            UOKE_DEBUG && $e->show();
        }
    }

    public static function appSystemError() {
        if (extension_loaded('zlib')) {
            ob_end_flush();
        }
        UOKE_DEBUG && Helper\Log::saveLog();
        if ($e = error_get_last()) {
            echo (new Uoke\uError($e, E_ERROR))->show();
        }
    }

}
