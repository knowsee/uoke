<?php

namespace Config\TraitConfig;

trait Route {

	public $routeList = [];
    public function routeInit() {
		
        foreach ($this->routeList as $route) {
			$this->route->addRoute($route[0], $route[1], $route[2]);
		}
		
    }

}
