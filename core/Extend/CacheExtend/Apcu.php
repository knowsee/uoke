<?php declare(strict_types = 1);
namespace CacheExtend;
class Apcu {

	public function __construct(array $config) {
		return $this;
	}

	public function set(string $key, string $value, int $life = 3600) : bool {
		if ($life == 0) $life = null;
		return apcu_add($key, $value, $life);
	}

	public function get(string $key) : string {
		return apcu_fetch($key);
	}

	public function delete(string $key) : bool {
		return apcu_delete($key);
	}

	public function strongAdd(string $key, string $value, int $life = 3600) : bool {
		if ($life == 0) $life = null;
		return apcu_store($key, $value, $life);
	}

	public function clear_all() : bool {
		return apcu_clear_cache();
	}

	public function exists(string $key) : bool {
		return apcu_exists($key);
	}

	public function inc(string $key, int $step) : bool {
		return apcu_inc($key, $step);
	}

	public function dec(string $key, int $step) : bool {
		return apcu_dec($key, $step);
	}

}