<?php
namespace Factory;
use Uoke\Request\Exception;

class UriRule {

    private $urlType = 1;
    private $urlModule = null;
    /**
     * uriParse need parse which rule use
     * ['\Factory\Uri\{pathInfoRule|apacheRule|defaultRule}']
     * @var array
     */
    private $handleClass = array(
        '1' => '\\Factory\\Uri\\DefaultRule',
    );

    public function __construct() {
        $this->urlType = CONFIG('urlRule/type');
        $this->handleClass = CONFIG('urlRule/handleClass');
        if(isset($this->handleClass[$this->urlType]) == false) {
            throw new Exception('Request handle model not found');
        } else {
            $this->urlModule = \app::createObject($this->handleClass[$this->urlType]);
        }
    }

    public function getModel() {
        return $this->urlModule->getUrlModel();
    }

    /**
     * @param $module eg Index:Test
     * @param $param
     * @param string $urlName
     * @param string $siteName
     * @return string
     */
    public function makeParseUrl($module, $param, $urlName = '', $siteName = 'default') {
        list($actionName, $moduleName) = $this->parseModule($module);
        $param['m'] = $moduleName;
        $param['a'] = $actionName;
        $getParseUrl = $this->urlModule->makeUrl($param, $urlName);
        return CONFIG('siteUrl/'.$siteName).$getParseUrl;
    }


    private function parseModule($module) {
        return explode(':', $module);
    }
}