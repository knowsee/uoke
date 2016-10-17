<?php declare(strict_types = 1);
namespace CacheExtend;
class Memcached {
	 
	 private $config = array();
	 private $link = NULL;
	 
	 public function __construct(array $config) {
		 $this->config = $config;
		 $unNum = to_guid_string($config);
		 $this->link = new Memcached($unNum);
		 $this->link->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
		 if (!count($this->link->getServerList())) {
			 foreach($config as $server) {
				 $this->link->addServer($server['hosts'], $server['port'], $server['weight']);
			 }
		}
		return $this;
	 }
	 
	 public function set(string $key, string $value, int $life = 3600) : bool {
		 return $this->link->set($key, $value, $life);
	 }
	 
	 public function getCode() {
		 return $this->link->getResultCode();
	 }
	 
	 public function get(string $key) : string {
		 $get = $this->link->get($key);
		 return $get == false ? '' : $get;
	 }
	 
	 public function replace(string $key, string $value, int $life = 3600) : bool {
		 return $this->link->replace($key, $value, $life);
	 }
	 
	 public function delete(string $key) : bool {
		 return $this->link->delete($key);
	 }
 }
