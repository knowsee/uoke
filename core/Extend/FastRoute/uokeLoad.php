<?php

namespace FastRoute;

require __DIR__ . '/functions.php';

class uokeLoad {
	
	public static function Load() {
		spl_autoload_register(function($class) {
			if (strpos($class, 'FastRoute\\') === 0) {
				$name = substr($class, strlen('FastRoute'));
				require __DIR__ . strtr($name, '\\', DIRECTORY_SEPARATOR) . '.php';
			}
			return true;
		}, false, true);
	}
}