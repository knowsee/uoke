<?php

namespace Uoke;

use Uoke\Request\Client,
    Uoke\Request\Server,
    Helper\Json;

class Controller {

    private $view = array();
    private $returnType = 'html';
    private $returnClient = array(
        'message',
        'code',
        'data'
    );
    protected $client = null;
    protected $server = null;
    protected $array = null;
    
    public $pageNum = 20;
    public $pageName = 'page';
    public $limitName = 'getNum';
    
    public $page = 1;

    const RETURN_TYPE_HTML = 'html';
    const RETURN_TYPE_JSON = 'json';
    const MESSAGE_STATUS_OK = 200;
    const MESSAGE_STATUS_ERROR = 500;
    const MESSAGE_STATUS_BAN = 403;

    /**
     *  Message redirect time (s)
     */
    const MESSAGE_SECOND = 3;

    /**
     * Handle with Get pageName && limitName to set Page && LimitRow
     * @return $this
     */
    public function __construct() {
        $this->client = Client::getInstance();
        $this->server = Server::getInstance();
        list($this->page, $this->pageNum) = $this->pageLimitInit();
        return $this;
    }
    
    /**
     * 301跳转
     * @param $moduleUrl
     * @param array $args
     * @param int $second
     */
    public function redirect($moduleUrl, $args = array(), $second = self::MESSAGE_SECOND) {
        $pathUrl = $this->excUrl($moduleUrl, $args);
        $second && $this->callClass('server')->setSleep($second);
        header("Location: " . $pathUrl);
    }

    /**
     * Html 常规提示输出
     * @param $message
     * @param $moduleUrl
     * @param int $second
     */
    public function rightWithWeb($message, $moduleUrl, $second = self::MESSAGE_SECOND) {
        
    }

    /**
     * Html 错误提示输出
     * @param $message
     * @param $moduleUrl
     * @param int $second
     */
    public function errorWithWeb($message, $moduleUrl, $second = self::MESSAGE_SECOND) {
        
    }

    /**
     * Json List 常规输出
     * @param array $data
     * @param int $total
     * @param int $page
     */
    public function listWithJson(array $data, int $total, int $page = 1) {
        $totalPage = ceil($total / $this->pageNum);
        $this->returnType = self::RETURN_TYPE_JSON;
        $this->returnClient['data'] = array(
            'page' => $page,
            'totalPage' => $totalPage,
            'total' => $total,
            'list' => $data
        );
        $this->returnClient['code'] = self::MESSAGE_STATUS_OK;
        $this->showMsg('List');
    }

    /**
     * Json 常规提示输出
     * @param array $data
     * @param string $message
     * @param int $status
     */
    public function rightWithJson(array $data = array(), string $message = 'Message Ok', int $status = self::MESSAGE_STATUS_OK) {
        $this->returnType = self::RETURN_TYPE_JSON;
        $this->returnClient['data'] = $data;
        $this->returnClient['code'] = $status;
        $this->showMsg($message);
    }

    /**
     * Json 错误提示输出
     * @param array $errorDetail
     * @param string $message
     * @param int $status
     */
    public function errorWithJson(string $message = 'Message Error', array $errorDetail = array(), int $status = self::MESSAGE_STATUS_ERROR) {
        $this->returnType = self::RETURN_TYPE_JSON;
        $this->returnClient['code'] = $status;
        $this->returnClient['data']['errorDetail'] = $errorDetail;
        $this->showMsg($message);
    }

    /**
     * 提示输出
     * @param $message
     * @param $moduleUrl
     * @param string $template
     * @param int $second
     */
    public function showMsg($message, $moduleUrl = '', $template = '', $second = self::MESSAGE_SECOND) {
        if ($this->returnType == self::RETURN_TYPE_HTML) {
            echo $message;
        } elseif ($this->returnType == self::RETURN_TYPE_JSON) {
            header("Content-type: application/json");
            echo Json::encode(array(
                'message' => $message,
                'code' => $this->returnClient['code'],
                'data' => $this->returnClient['data'],
            ));
        }
        exit;
    }

    /**
     * 页面变量输出
     * @param string $name
     * @param mixed $value
     */
    public function view(string $name, $value = '') {
        if (is_array($name)) {
            foreach ($name as $viewKey => $viewData) {
                $this->view[$viewKey] = $viewData;
            }
        } else {
            $this->view[$name] = $value;
        }
    }

    /**
     * 输出页面
     * @param string $filename
     */
    public function display(string $filename) {
        if (!IS_CLI) {
            extract($this->view);
            require MAIN_PATH . CONFIG('templateDir') . $filename . '.php';
        } else {
            var_dump($this->view);
        }
    }

    public static function loadTemplate($filename) {
        require MAIN_PATH . CONFIG('templateDir') . $filename . '.php';
    }

    /**
     * Url生成方法
     * @param string $moduleName
     * @param array $args is query Array
     * @param string $ruleName
     * @return string url
     */
    public function excUrl($moduleName, $args = array(), $ruleName = '') {
        $urlModule = \app::createObject('\Factory\UriFast');
        return $urlModule->makeParseUrl($this->handleModule($moduleName), $moduleName, $args, $ruleName);
    }
    
    private function pageLimitInit() {
        $page = $this->client->get($this->pageName, 1);
        if($page < 1) {
            $page = 1;
        }
        $limit = $this->client->get($this->limitName, $this->pageNum);
        if($limit < 1) {
            $limit = 1;
        }
        return array(intval($page), intval($limit));
    }

    private function handleModule($actionModule) {
        if (strstr($actionModule, '/') == false) {
            list($appName, $actionModule) = explode('\\', \app::$CONTROLLER);
            return $actionModule;
        } else {
            list($actionModule) = explode('/', $actionModule);
            return $actionModule;
        }
    }

}
