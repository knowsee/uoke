<?php
namespace Factory\Uri;
use Adapter\Uri as UriAdapter;
use Uoke\Request\Client;

class PathInfoRule implements UriAdapter {

    private $paramGet = array();
    private $paramUri = null;
    private $_Client = null;
    private $rule = array();

    public function __construct() {
        $this->_Client = Client::getInstance();
        $this->paramUri = $this->_Client->getWebPathInfo();
        $this->paramGet = $this->_Client->get();
        $this->rule = CONFIG('urlRule/path');
    }

    public function getUrlModel() {
        return $this->handleUrl();
    }

    public function getRule() {
        // TODO: Implement getRule() method.
    }

    public function setRule() {
        // TODO: Implement setRule() method.
    }

    private function handleUrl() {
        $urlPathInfo = explode('/', $this->paramUri);
        for ($u = 1; $u < count($urlPathInfo); $u++) {
            if ($u < 3) {
                $module[] = $urlPathInfo[$u];
            } else {
                $modulePathUrl[] = $urlPathInfo[$u];
            }
        }
        $this->findRule($module, $modulePathUrl);
        return $module;
    }

    private function findRule($path, $paramValue) {
        $pathKey = implode('_', $path);
        if(isset($this->rule[$pathKey])) {
            $ruleString = $this->rule[$pathKey];
        }
        $rule = explode('/', $ruleString);
        for ($u = 1; $u < count($rule); $u++) {
            if($paramValue[($u-1)]) {
                $this->_Client->setQueryKeyParam([$rule[$u]], $paramValue[($u-1)]);
            }
        }

    }
}