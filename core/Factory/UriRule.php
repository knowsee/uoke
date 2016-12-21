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
        '1' => '\Factory\Uri\DefaultRule',
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
     * @return string
     */
    public function makeParseUrl($module, $param, $urlName = '') {
        list($siteName, $actionName, $moduleName) = $this->parseModule($module);
        $siteUrl = CONFIG('siteUrl/'.$siteName);
        $param['a'] = $actionName;
        $param['m'] = $moduleName;
        $getParseUrl = $this->urlModule->makeUrl($param, $urlName);
        return $siteUrl.$getParseUrl;
    }

    /*
     * @desc ParseModule
     * @param string $callAction
     * @return string
     */
    private function parseModule($callAction) {
        list($actionName, $moduleName) = explode(':', $callAction);
        $actionList = array_values(array_filter(explode('\\', $actionName)));
        switch (count($actionList)) {
            case 3:
                $siteName = $actionList[0];
                $actionName = $actionList[2];
                break;
            case 2:
                $siteName = 'default';
                $actionName = $actionList[1];
                break;
        }
        return array($siteName, $actionName, $moduleName);
    }
}