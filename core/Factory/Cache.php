<?php declare(strict_types = 1);
namespace Factory;
use CacheExtend\{Apcu,File,Memcached,Redis};
class Cache {

    private static $cacheInstance = array();

    const CACHE_NAME_MEMCACHED = 1;
    const CACHE_NAME_APCU = 2;
    const CACHE_NAME_REDIS = 3;
    const CACHE_NAME_FILE = 4;

    public static function getInstance(int $type) {
        if (isset(self::$cacheInstance[$type])) {
            return self::$cacheInstance[$type];
        }
        switch ($type) {
            case self::CACHE_NAME_MEMCACHED:
                self::$cacheInstance[self::CACHE_NAME_MEMCACHED] = self::getMemcached();
                break;
            case self::CACHE_NAME_APCU:
                self::$cacheInstance[self::CACHE_NAME_APCU] = self::getApcu();
                break;
            case self::CACHE_NAME_REDIS:
                self::$cacheInstance[self::CACHE_NAME_REDIS] = self::getRedis();
                break;
            case self::CACHE_NAME_FILE:
				self::$cacheInstance[self::CACHE_NAME_FILE] = self::getFile();
				break;
        }
        return self::$cacheInstance[$type];
    }

    private static function getMemcached() : Memcached {
        return new Memcached(CONFIG('cache/memcached'));
    }

    private static function getApcu() : Apcu {
        return new Apcu();
    }

    private static function getRedis() : Redis {
        return new Redis(CONFIG('cache/redis'));
    }
    
    private static function getFile() : File {
		return new File(CONFIG('cache/file'));
	}
}
