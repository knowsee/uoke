<?php
namespace Factory;
use Uoke\Request\Exception, FastRoute\uokeLoad;
use Uoke\Request\Client;
class UriFast {
	use \Config\TraitConfig\Route;
	private $_Client = null;
	public function runRoute() {
		uokeLoad::Load();
		$this->_Client = Client::getInstance();
		$dispatcher = $this->routeBase();
		// Fetch method and URI from somewhere
		$httpMethod = $_SERVER['REQUEST_METHOD'];
		$uri = $_SERVER['REQUEST_URI'];
		// Strip query string (?foo=bar) and decode URI
		if (false !== $pos = strpos($uri, '?')) {
			$uri = substr($uri, 0, $pos);
		}
		$uri = rawurldecode($uri);
		$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
		switch ($routeInfo[0]) {
			case \FastRoute\Dispatcher::NOT_FOUND:
				return $this->parseUri($uri);
			case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
					$allowedMethods = $routeInfo[1];
				throw new \Uoke\uError('Not allowed to connect', 405);
			case \FastRoute\Dispatcher::FOUND:
					$handler = $routeInfo[1];
					$vars = $routeInfo[2];
					$this->setRouteVal($vars);
				return $this->parseUri($this->handlerRouteAction($handler));
		}
	}
	
	private $route;
	
	private function routeBase() {
		$dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
			$this->route = $r;
			$this->routeList();
		}, [
			'cacheFile' => MAIN_PATH.'Data/System/route.cache', /* required */
			'cacheDisabled' => UOKE_DEBUG,     /* optional, enabled by default */
		]);
		return $dispatcher;
	}
	
	private function setRouteVal($val) {
		foreach($val as $valKey => $value) {
			$this->_Client->setQueryKeyParam($valKey, $value);
		}
	}
	
	/**
	**	路由指向器
	**
	**/
	private function handlerRouteAction($actionHandler) {
		return explode('/', $actionHandler);
	}
	
	private function routeList() {
		$routeList = $this->routeIndex();
		foreach($routeList as $route) {
			$this->route->addRoute($route[0], $route[1], $route[2]);
		}
	}
	
	private function parseUri($uri) {
		$uriInfo = pathinfo($uri);
		if($uriInfo['basename'] == 'index.php') {
			return $this->defaultUri();
		} else {
			return $this->isPathInfo($uri);
		}
	}
	
	private function defaultUri() {
        $param = $this->_Client->get();
		return [
			$param['Action'],
			$param['Module']
		];
	}
	
	private function isPathInfo($uri) {
		$urlPathInfo = array_values(array_filter(explode('/', $uri)));
		return $urlPathInfo;
	}
	
}