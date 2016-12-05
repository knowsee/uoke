<?php
define('IN_UOKE', TRUE);
defined('MAIN_PATH') or define('MAIN_PATH', dirname(__FILE__).'/');
defined('UOKE_DEBUG') or define('UOKE_DEBUG', false);
define('SYSTEM_PATH', dirname(__FILE__).'/');
defined('LOAD_CONFIG') or define('LOAD_CONFIG', SYSTEM_PATH.'Config/');
defined('APP_NAME') or define('APP_NAME', 'default');
define('LOAD_SYSTEM_CONFIG', SYSTEM_PATH.'Config/');
define('ICONV_ENABLE', function_exists('iconv'));
define('MB_ENABLE', function_exists('mb_convert_encoding'));
define('EXT_OBGZIP', function_exists('ob_gzhandler'));
define('UNIXTIME', time());
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CGI', (0 === strpos(PHP_SAPI, 'cgi') || false !== strpos(PHP_SAPI, 'fcgi')) ? 1 : 0 );
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
define('GLOBAL_KEY', 'UOKE_');

require SYSTEM_PATH.'core.php';
Core::start();
class app {

    private static $classMap = array();
    public static $CONTROLLER = null;
    public static $coreConfig = array();

    public static function run() {
        if(empty(static::$coreConfig)) {
            static::loadConfig();
        }
        if(SYSTEM_PATH !== MAIN_PATH) {
            define('IS_APP', true);
        } else {
            define('IS_APP', false);
        }
        try {
            $url = self::createObject('\Factory\UriRule');
            list($action, $module) = $url->getModel();
            self::goIndex($action, $module);
        } catch (\Uoke\uError $e) {
            UOKE_DEBUG && var_dump($e->getMessage());
        }
    }

    private static function goIndex($action, $module) {
        if(empty($action)) {
            self::goDefaultPage();
            return true;
        } else {
            self::$CONTROLLER = '\\Action\\'.self::handleAction($action);
            self::runAction($module);
            return true;
        }
    }

    private static function goDefaultPage() {
        $defaultAction = static::$coreConfig['defaultAction']['siteIndex'];
        self::$CONTROLLER = '\\Action\\'.self::handleAction($defaultAction['action']);
        self::runAction($defaultAction['action'], $defaultAction['module']);
    }

    private static function handleAction($action) {
        $smartAction = implode('_', $action['action']);
        if(!$smartAction && is_string($action)) {
            return $action;
        } elseif(!$smartAction && is_array($action)) {
            return $action[0];
        } else {
            return $smartAction;
        }
    }

    private static function runAction($module) {
        if(defined('APP_NAME')) {
            self::$CONTROLLER = '\\'.APP_NAME.self::$CONTROLLER;
        }
        $controller = self::createObject(self::$CONTROLLER);
        if($module && method_exists($controller, $module)) {
            $controller->$module();
        } elseif(empty($module) && method_exists($controller, static::$coreConfig['defaultAction']['actionIndex'])) {
            $module = static::$coreConfig['defaultAction']['actionIndex'];
            $controller->$module();
        } else {
            throw new \Uoke\uError(E_ERROR,'Action not found');
        }
    }

    private static function loadConfig() {
        $cache = array();
        if(LOAD_SYSTEM_CONFIG !== LOAD_CONFIG) {
            $cacheMainFile = static::makeAppConfig();
            require $cacheMainFile;
        } else {
            $cacheMainFile = static::makeSystemConfig();
            require $cacheMainFile;
        }
        static::$coreConfig = strdepack($cache);
        header("Content-type: text/html; charset=utf-8");
    }

    private static function makeAppConfig() {
        $cacheMainFile = getCacheFile(MAIN_PATH.'Data/System/runtime~');
        if(!$cacheMainFile) {
            $cache = array();
            $systemConfigFile = static::makeSystemConfig();
            require $systemConfigFile;
            $systemConfig = strdepack($cache);
            $systemConfig['cacheName'] = '_config';
            setCacheFile(array(
                LOAD_CONFIG . 'app.php',
                LOAD_CONFIG . 'db.php',
                LOAD_CONFIG . 'cache.php',
            ), MAIN_PATH.'Data/System/runtime~', $systemConfig);
            $cacheMainFile = getCacheFile(MAIN_PATH.'Data/System/runtime~');
        }
        return $cacheMainFile;
    }

    private static function makeSystemConfig() {
        $cacheMainFile = getCacheFile(SYSTEM_PATH.'Data/System/runtime~');
        if(!$cacheMainFile) {
            $systemConfig['cacheName'] = '_config';
            $cacheFile = getCacheFile(SYSTEM_PATH.'Data/System/runtime~');
            if($cacheFile == false) {
                setCacheFile(array(
                    LOAD_SYSTEM_CONFIG . 'app.php',
                    LOAD_SYSTEM_CONFIG . 'db.php',
                    LOAD_SYSTEM_CONFIG . 'cache.php',
                ), SYSTEM_PATH.'Data/System/runtime~', $systemConfig);
            }
            $cacheMainFile = getCacheFile(SYSTEM_PATH.'Data/System/runtime~');
        }
        return $cacheMainFile;
    }

    /**
     * Creates a new object using the given configuration.
     *
     * // create an object using a class name
     * $object = \app::createObject('NAMESPACE\CLASS');
     *
     * // create an object with two constructor parameters
     * $object = \app::createObject('MyClass', [$param1, $param2]);
     * @param string $type
     * @param array $params
     * @return object the created object
     * @throws Exception
     */
    public static function createObject($type, array $params = []) {
        if (is_string($type)) {
            $args = func_get_args();
            $config = static::handleArgs($args);
            return static::returnClass($type, $params, $config);
        } elseif (is_callable($type, true)) {
            return static::invoke($type, $params);
        } elseif (is_array($type)) {
            throw new Exception('Object configuration must be an array containing a "class" element.');
        } else {
            throw new Exception('Unsupported configuration type: ' . gettype($type));
        }
    }

    private static function returnClass($className, $params = [], $config = []) {
        try {
            $classKeyName = to_guid_string($className);
            if(isset(self::$classMap[$classKeyName]) == false) {
                self::$classMap[$classKeyName] = new $className(...$config);
            }
            if($params) {
                return call_user_func_array(self::$classMap[$classKeyName], $params);
            } else {
                return self::$classMap[$classKeyName];
            }
        } catch (\Uoke\uError $e) {
            UOKE_DEBUG && var_dump($e->getMessage());
        }

    }

    private static function invoke(callable $callback, $params = []) {
        return call_user_func_array($callback, $params);
    }

    private static function handleArgs($args) {
        unset($args[0], $args[1]);
        $callArgs = array();
        foreach($args as $field) {
            $callArgs[] = $field;
        }
        return $callArgs;
    }

}