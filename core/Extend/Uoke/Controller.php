<?php
namespace Uoke;
use Uoke\Request\Client, Uoke\Request\Server, Helper\Json;
class Controller {
    private $view = array();
    private $returnType = 'html';
    private $returnClient = array(
        'message',
        'code',
        'data'
    );

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
     * 魔法方法
     * @param $name
     * @param $arguments
     * @return mixed|Client|Server|array
     */
    public function __call($name, $arguments) {
        return $this->callClass($name, $arguments);
    }

    /**
     * 魔法获取
     * @param $name
     * @return mixed|Client|Server
     */
    public function __get($name) {
        return $this->callClass($name);
    }

    /**
     * 301跳转
     * @param $moduleUrl
     * @param array $args
     * @param int $second
     */
    public function redirect($moduleUrl, $args = array(), $second = self::MESSAGE_SECOND) {
        $pathUrl = '';
        if(!isUrl($moduleUrl)) {
            /**
             * Module Url support waiting
             */
            $pathUrl = $this->excUrl($moduleUrl, $args);
        }
        $second && $this->callClass('server')->setSleep($second);
        header("Location: ".$this->callClass('client')->getServerName().$pathUrl);
        if(function_exists('fastcgi_finish_request')) fastcgi_finish_request();
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
     * Json 常规提示输出
     * @param array $data
     * @param string $message
     * @param int $status
     */
    public function rightWithJson(array $data, string $message = 'Message Ok', int $status = self::MESSAGE_STATUS_OK) {
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
    public function errorWithJson(array $errorDetail, string $message = 'Message Error', int $status = self::MESSAGE_STATUS_ERROR) {
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
        if($this->returnType == self::RETURN_TYPE_HTML) {
            echo $message;
        } elseif($this->returnType == self::RETURN_TYPE_JSON) {
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
        if(!IS_CLI) {
            extract($this->view);
            require MAIN_PATH . CONFIG('templateDir') . $filename . '.php';
        } else {
            var_dump($this->view);
        }
    }

    /**
     * Url生成方法
     * @param $moduleName like Module:Action(Index:Index)
     * @param array $args is query Array
     * @return string url
     */
    public function excUrl($moduleName, $args = array()) {
        return;
    }

    private function callClass($name, $arguments = '') {
        switch ($name) {
            case 'array':
                $arguments = !$arguments ? array(CONTROLLER) : $arguments;
                return call_user_func_array('\Helper\cArray::getInstance', $arguments);
            case 'client':
                return Client::getInstance();
            case 'server':
                return Server::getInstance();
        }
    }


}