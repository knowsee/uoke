<?php declare(strict_types = 1);
namespace CacheExtend;
class Redis {

	private $link = NULL;
	private $defaultChoose = 'global';
	private $config = array();

	public function __construct(array $config) {
		foreach($config as $key => $server) {
			$this->link[$key] = new Redis();
			$this->link[$key]->pconnect($server['hosts'], $server['port']);
			$this->link[$key]->setOption(Redis::OPT_PREFIX, $server['pre']);
		}
		return $this;
	}

	private function getLink() : Redis {
		return $this->link[$this->defaultChoose];
	}

	public function set(string $key, string $value, int $life = 3600) : bool {
		$return = $this->getLink()->set($key, $value);
		if ($life > 0)
			$this->getLink()->expire($key, $life);
		return $return;
	}
	
	public function setByfeild(string $table, string $field, array $value, int $life = '') {
		if(!$this->exists($table)) {
            $setTime = true;
        }
		$return = $this->hset($table, strpack($field), $value);
		if($setTime == true && $life > 0) {
            $this->expire($table, $life);
        }
        return $return;
	}

	public function sets(array $keyArray, int $life) : bool {
		if (is_array($keyArray)) {
			$retRes = $this->getLink()->mset($keyArray);
			if ($life > 0) {
				foreach ($keyArray as $key => $value) {
					$this->getLink()->expire($key, $life);
				}
			}
			return $retRes;
		} else {
			return false;
		}
	}

    public function expire(string $key, int $life) : bool {
        return $this->getLink()->expire($key, $life);
    }

    public function exists(string $key) : bool {
        return $this->getLink()->exists($key);
    }

	public function get(string $key) : string {
		$get = $this->getLink()->get($key);
		return $get == false ? '' : $get;
	}
	
	public function getByfeild(string $table, string $feild) : string {
		$get = $this->getLink()->get($key);
		return $get == false ? '' : strdepack($get);
	}

	public function gets(array $keyArray) : array {
		if (is_array($keyArray)) {
			return $this->getLink()->mget($keyArray);
		} else {
			return array();
		}
	}

    public function delete(string $key) : int {
        return $this->getLink()->delete($key);
    }
    
    public function deleteByfeild(string $table, string $feild) : bool {
        return $this->getLink()->hDel($table, $key);
    }

	public function hset(string $tableName, string $field, string $value) : bool {
		$return = $this->getLink()->hset($tableName, $field, $value);
		if($return == false) {
			return false;
		} else {
			return true;
		}
	}

	public function hget(string $tableName, string $field) : string {
		$return = $this->getLink()->hget($tableName, $field);
		return $return == false ? '' : $return;
	}

	public function hdelete(string $tableName, string $field) : bool {
		$return = $this->getLink()->hDel($tableName, $field);
		return $return;
	}

    public function hKeys(string $tableName) : array {
        return $this->getLink()->hKeys($tableName);
    }

    public function hExists(string $tableName, string $field) : bool {
        return $this->getLink()->hExists($tableName, $field);
    }

}
