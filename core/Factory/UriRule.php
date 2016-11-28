<?php
namespace Factory;
use Uoke\Request\Exception;

class UriRule {

    /**
     * uriParse need parse which rule use
     * ['\Factory\Uri\{pathInfo|apacheRule|defaultRule}']
     * @var array
     */

    private $urlType = 1;
    private $urlModule = null;
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


    private function handleUrl() {

    }
}