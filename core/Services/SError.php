<?php
namespace Services;
class SError {
    
    const DEFAULT_ERROR = 0;
    const IMPORTANT_ERROR = 1;
    const ARGS_EMPTY = 2;
    
    public function __construct(int $errorCode, $message) {
        switch ($errorCode) {
            case self::DEFAULT_ERROR:
                throw new \Exception($message, E_NOTICE);
            case self::IMPORTANT_ERROR:
                throw new \Uoke\uError($message, E_ERROR);
            case self::ARGS_EMPTY:
                throw new \Exception($message, E_CORE_ERROR);
        }
    }
    
    public function __toString() {
        ;
    }
}
