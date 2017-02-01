<?php
namespace Factory\Uri;
use Adapter\Uri as UriAdapter;
use Uoke\Request\Client;

class DefaultRule implements UriAdapter {

    private $paramGet = array();
    private $paramUri = null;
    private $_Client = null;

    public function __construct() {
        $this->_Client = Client::getInstance();
        $this->paramUri = $this->_Client->getWebPathInfo();
        $this->paramGet = $this->_Client->get();
    }

    public function getUrlModel()
    {
        return array($this->paramGet['a'], $this->paramGet['m']);
    }

    public function makeUrl($param, $urlName = '')
    {
        $param = array_filter($param);
        return 'index.php?'.http_build_query($param);
    }

    public function getRule() {
        return;
    }

    public function setRule() {
        return;
    }
}