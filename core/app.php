<?php

define('IN_UOKE', TRUE);
defined('MAIN_PATH') or define('MAIN_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
defined('UOKE_DEBUG') or define('UOKE_DEBUG', false);
define('SYSTEM_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
defined('LOAD_CONFIG') or define('LOAD_CONFIG', SYSTEM_PATH . 'Config' . DIRECTORY_SEPARATOR);
defined('APP_NAME') or define('APP_NAME', 'default');
define('LOAD_SYSTEM_CONFIG', SYSTEM_PATH . 'Config' . DIRECTORY_SEPARATOR);
define('ICONV_ENABLE', function_exists('iconv'));
define('MB_ENABLE', function_exists('mb_convert_encoding'));
define('EXT_OBGZIP', function_exists('ob_gzhandler'));
define('UNIXTIME', time());
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CGI', (0 === strpos(PHP_SAPI, 'cgi') || false !== strpos(PHP_SAPI, 'fcgi')) ? 1 : 0 );
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
define('GLOBAL_KEY', 'UOKE_');

require SYSTEM_PATH . 'core.php';
Core::start();
class app {

    private static $classMap = array();
    public static $CONTROLLER = null;
    public static $coreConfig = array();

    public static function run() {
        if (empty(static::$coreConfig)) {
            static::loadConfig();
        }
        define('CHARSET', CONFIG('charset'));
        try {
            $url = self::createObject('\Factory\UriFast');
            list($action, $module) = $url->runRoute();
            if (!$action) {
                self::goDefaultPage();
            } else {
                self::goPage($action, $module);
            }
        } catch (\Uoke\uError $e) {
            $http = self::createObject('\Uoke\Request\HttpException', array(), $e->code());
            $http->showCode($e);
        }
    }
    
    public static function setLang($lang) {
        $lang = strtolower($lang);
        if(in_array($lang, self::$coreConfig['langConfig'])) {
            self::$coreConfig['viewLang'] = $lang;
        } else {
            self::$coreConfig['viewLang'] = self::$coreConfig['lang'];
        }
        return self::$coreConfig['viewLang'];
    }
    
    public static function getLang() {
        return self::$coreConfig['viewLang'];
    }

    private static function goDefaultPage() {
        $defaultAction = static::$coreConfig['defaultAction']['siteIndex'];
        self::$CONTROLLER = '\\Action\\' . self::handleAction($defaultAction['action']);
        self::runAction($defaultAction['action'], $defaultAction['module']);
    }

    private static function goPage($action, $module) {
        self::$CONTROLLER = '\\Action\\' . self::handleAction($action);
        if (!$module) {
            $module = static::$coreConfig['defaultAction']['actionIndex'];
        }
        self::runAction($module);
    }

    private static function handleAction($action) {
        $smartAction = implode('_', $action['action']);
        if (!$smartAction && is_string($action)) {
            return $action;
        } elseif (!$smartAction && is_array($action)) {
            return $action[0];
        } else {
            return $smartAction;
        }
    }

    private static function runAction($module) {
        if (defined('APP_NAME') && APP_NAME !== 'default') {
            self::$CONTROLLER = '\\' . APP_NAME . self::$CONTROLLER;
        }
        define('A', self::$CONTROLLER);
        define('M', $module);
        $controller = new self::$CONTROLLER();
        if ($module && method_exists($controller, $module)) {
            $controller->$module();
        } else {
            throw new \Uoke\uError('Connect is fail', 404);
        }
    }

    private static function loadConfig() {
        $cache = array();
        if (LOAD_SYSTEM_CONFIG !== LOAD_CONFIG) {
            $cacheMainFile = static::makeAppConfig();
            require $cacheMainFile;
        } else {
            $cacheMainFile = static::makeSystemConfig();
            require $cacheMainFile;
        }
        static::$coreConfig = strdepack($cache);
    }

    private static function makeAppConfig() {
        $cacheMainFile = getCacheFile(MAIN_PATH . 'Data/System/runtime~');
        if (!$cacheMainFile || UOKE_DEBUG == true) {
            $cache = array();
            $systemConfigFile = static::makeSystemConfig();
            require $systemConfigFile;
            $systemConfig = strdepack($cache);
            $systemConfig['cacheName'] = '_config';
            setCacheFile(array(
                LOAD_CONFIG . 'app.php',
                LOAD_CONFIG . 'db.php',
                LOAD_CONFIG . 'cache.php',
                    ), MAIN_PATH . 'Data/System/runtime~', $systemConfig);
            $cacheMainFile = getCacheFile(MAIN_PATH . 'Data/System/runtime~');
        }
        return $cacheMainFile;
    }

    private static function makeSystemConfig() {
        $cacheMainFile = getCacheFile(SYSTEM_PATH . 'Data/System/runtime~');
        if (!$cacheMainFile || UOKE_DEBUG == true) {
            $systemConfig['cacheName'] = '_config';
            setCacheFile(array(
				LOAD_SYSTEM_CONFIG . 'app.php',
				LOAD_SYSTEM_CONFIG . 'db.php',
				LOAD_SYSTEM_CONFIG . 'cache.php',
					), SYSTEM_PATH . 'Data/System/runtime~', $systemConfig);
            $cacheMainFile = getCacheFile(SYSTEM_PATH . 'Data/System/runtime~');
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
     * @throws \Uoke\uError
     */
    public static function createObject($type, array $params = []) {
        if (is_string($type)) {
            $args = func_get_args();
            $config = static::handleArgs($args);
            return static::returnClass($type, $params, $config);
        } elseif (is_callable($type, true)) {
            return static::invoke($type, $params);
        } elseif (is_array($type)) {
            throw new \Uoke\uError('Object configuration must be an array containing a "class" element.');
        } else {
            throw new \Uoke\uError('Unsupported configuration type: ' . gettype($type));
        }
    }

    private static function returnClass($className, $params = [], $config = []) {
        $classKeyName = to_guid_string($className);
        if (isset(self::$classMap[$classKeyName]) == false && class_exists($className)) {
            self::$classMap[$classKeyName] = new $className(...$config);
        }
        if ($params) {
            return call_user_func_array(self::$classMap[$classKeyName], $params);
        } else {
            return self::$classMap[$classKeyName];
        }
    }

    private static function invoke(callable $callback, $params = []) {
        return call_user_func_array($callback, $params);
    }

    private static function handleArgs($args) {
        unset($args[0], $args[1]);
        $callArgs = array();
        foreach ($args as $field) {
            $callArgs[] = $field;
        }
        return $callArgs;
    }

}
