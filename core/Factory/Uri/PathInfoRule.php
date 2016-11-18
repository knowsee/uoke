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
        if(IS_CLI) {
            $this->paramGet = $this->_Client->getCli();
            $this->paramUri = $this->paramGet[0];
        } else {
            $this->paramUri = $this->_Client->getWebPathInfo();
            $this->paramGet = $this->_Client->get();
        }
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

    public function makeUrl($param, $urlName = '') {
        $ruleString = $this->findRuleKey(array($param['a'], $param['m']));

    }

    private function handleUrl() {
        $urlPathInfo = explode('/', $this->paramUri);
        for ($u = 1; $u < count($urlPathInfo); $u++) {
            if($u == 1) {
                $action[0] = explode('_', $urlPathInfo[$u]);
            } elseif ($u == 2) {
                $action[1] = $urlPathInfo[$u];
            } else {
                $modulePathUrl[] = $urlPathInfo[$u];
            }
        }
        $this->parseRule($action, $modulePathUrl);
        return $action;
    }

    private function findRuleKey($path) {
        $pathKey = implode('_', $path[0]).'_'.$path[1];
        if(isset($this->rule[$pathKey])) {
            return $this->rule[$pathKey];
        }
    }

    private function parseRule($action, $paramValue) {
        $ruleString = $this->findRuleKey($action);
        $rule = array_filter(explode('/', $ruleString));
        for ($u = 1; $u <= count($rule); $u++) {
            if($paramValue[($u-1)]) {
                $this->_Client->setQueryKeyParam($rule[$u], $paramValue[($u-1)]);
            }
        }

    }
}