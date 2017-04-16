<?php
namespace Uoke\Request;
class HttpException {
	use \Config\TraitConfig\HttpCode;
	const NO_ALLOWED_METHOD = 405;
	const PAGE_NOT_FOUND = 404;
	const NO_ACCESS_ALLOWED = 403;
	const BAD_REQUEST = 400;
	const UOKE_ERROR = 500;
	
	private $code = 0;
	
    public function __construct($httpCode) {
		if(in_array($httpCode, [400, 401, 403, 404, 405])) {
			$this->code = $httpCode;
		} else {
			$this->code = self::UOKE_ERROR;
		}
		return $this;
    }
	
	public function showCode($thing = null) {
		if(IS_CLI) {
			$this->showToCli($thing);
		} else {
			$this->showToHtml($thing);
		}
	}
	
	private function showToHtml($thing) {
		$Page = \App::createObject('\Uoke\Controller');
		$Client = Client::getInstance();
		if($Client->getIsAjax() == true) {
			$Page->errorWithJson($thing->getMessage(), array(), $this->code);
		} else {
			$getError = $this->errorGo($this->code);
			if($getError == true) {
			    $Page->view('thing', $thing);
				$Page->display('Uoke/'.$this->code);
			}
		}
		return true;
	}
	
	private function showToCli($thing) {
		echo '['.$this->code.']'. $this->getCode($this->code) . PHP_EOL;
		echo $thing . PHP_EOL;
		exit;
	}
}