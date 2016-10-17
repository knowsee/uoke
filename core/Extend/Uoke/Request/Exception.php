<?php
namespace Uoke\Request;
use Uoke\uError;

class Exception extends uError {
    public function __construct($string, $code = E_WARNING) {
        parent::__construct($code, $string);
    }
}