<?php
namespace Helper;


class cArray implements \ArrayAccess {

    private static $instance = null;
    private $arrays = array();
    private $stringTitle = '';

    public function __construct($title = '') {
        $this->stringTitle = $title;
    }

    public static function getInstance($title = '') : cArray {
        if(isset(self::$instance[$title]) == false) {
            self::$instance[$title] = new self($title);
        }
        return self::$instance[$title];
    }

    public function offsetExists($offset)
    {
        $offset = $this->handleFieldsName($offset);
        if($this->stringTitle) {
            return isset($this->arrays[$this->stringTitle][$offset]) ? true : false;
        } else {
            return isset($this->arrays[$offset]) ? true : false;
        }
    }

    public function offsetGet($offset)
    {
        $offset = $this->handleFieldsName($offset);
        if($this->stringTitle) {
            return $this->arrays[$this->stringTitle][$offset];
        } else {
            return $this->arrays[$offset];
        }
    }

    public function offsetSet($offset, $value)
    {
        $offset = $this->handleFieldsName($offset);
        if($this->stringTitle) {
            $this->arrays[$this->stringTitle][$offset] = $value;
        } else {
            $this->arrays[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        $offset = $this->handleFieldsName($offset);
        if($this->stringTitle) {
            unset($this->arrays[$this->stringTitle][$offset]);
        } else {
            unset($this->arrays[$offset]);
        }
    }

    private function handleFieldsName($offset) {
        $keyArray = explode('/', $offset);
        switch ($keyArray[0]) {
            case '~':
                $keyName = $this->getPrivateKey($keyArray);
                break;
            default:
                $keyName = $this->getGlobalKey($keyArray);
                break;
        }
        return $keyName;
    }

    private function getPrivateKey($foundFields) {
        unset($foundFields[0]);
        $foundFields[] = CONTROLLER;
        return strtolower(implode('_',$foundFields));
    }

    private function getGlobalKey($foundFields) {
        $keyName = GLOBAL_KEY;
        $keyName .= implode('_',$foundFields);
        return strtolower($keyName);
    }
}