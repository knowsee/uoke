<?php
namespace Uoke;
use Uoke\Request\Client, Uoke\Request\Server;
class Controller {

    public function __call($name, $arguments) {
        return $this->callClass($name, $arguments);
    }

    public function __get($name) {
        return $this->callClass($name);
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