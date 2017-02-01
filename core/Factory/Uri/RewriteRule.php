<?php
namespace Factory\Uri;
use Adapter\Uri as UriAdapter;
use Uoke\Request\Client;

class RewriteRule implements UriAdapter {

    private $paramGet = array();
    private $paramUri = null;
    private $_Client = null;
    private $rule = array();
    private $ruleCache = null;

    public function __construct() {
        $this->_Client = Client::getInstance();
        if(IS_CLI) {
            $this->paramGet = $this->_Client->getCli();
            $this->paramUri = $this->paramGet[0];
        } else {
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
        if($urlName) {
            $ruleString = $this->findRuleKey($urlName);
            if($ruleString) {
                $ruleKey = $urlName;
            }
        } else {
            $ruleString = $this->findRuleKey(array($param['a'], $param['m']));
            $ruleKey = $this->RuleKey(array($param['a'], $param['m']));
        }
        if(empty($ruleKey)) {
            return false;
        }
		
        if(!$this->ruleCache[$ruleKey]) {
            preg_match('/[1-9a-zA-z]+/', $ruleString, $this->ruleCache[$ruleKey]['ruleMatch']);
            array_unshift($this->ruleCache[$ruleKey]['ruleMatch'],'a', 'm');
            $this->ruleCache[$ruleKey]['ruleFormat'] = preg_replace('/[1-9a-zA-z]+/', '%s', $ruleString);
        }
        return $this->handleMake($param, $this->ruleCache[$ruleKey]);
    }

    private function handleMake($param, $ruleParam) {
        $u = array();
		$url = implodeCatchSource('/',array($param['a'], $param['m']));
        foreach($ruleParam['ruleMatch'] as $val) {
            $u[] = $param[$val];
            if($param[$val]) {
                unset($param[$val]);
            }
        }
        return $url.vsprintf($ruleParam['ruleFormat'], $u).$this->lastHandleMake(array_filter($param));
    }

    private function lastHandleMake($param) {
        if($param) {
            return '?'.http_build_query($param);
        } else {
            return null;
        }
    }

    private function handleUrl() {
        $urlPathInfo = array_filter(explode('/', $this->_Client->getWebPathInfo()));
        resetArray($urlPathInfo);
        $action[0] = $urlPathInfo[0] ? explode('_', $urlPathInfo[0]) : null;
        $action[1] = $urlPathInfo[1] ? $urlPathInfo[1] : null;
        for ($u = 3; $u < count($urlPathInfo); $u++) {
                $modulePathUrl[] = $urlPathInfo[$u];
        }
        $this->parseRule($action, $modulePathUrl);
        return $action;
    }

    private function RuleKey($path) {
        return implodeCatchSource('_', $path[0]).'_'.$path[1];
    }

    private function findRuleKey($path) {
        $pathKey = $this->RuleKey($path);
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