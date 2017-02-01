<?php
namespace Helper;

class Test extends Obj {

    public static $t = array();

    public static function T() {
        self::$t = array(
            'n' => array('name' => 1, 'name2' => 'nbbbbb')
        );
        $e = [new static(self::$t['n'])];
        var_dump($e);
    }
}

class Obj {

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

}