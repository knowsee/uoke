<?php

namespace Uoke;

use Helper\Log;

/**
 * 异常处理与分析类
 *
 * @author chengyi
 */
class uError extends \ErrorException {

    public $errorInfo = [];
    public $trace = [];

    public function __construct($e, $errstr = E_NOTICE, $errfile = __FILE__, $errline = __LINE__) {
        if (is_numeric($e)) {
            parent::__construct($errstr, $e, $e, $errfile, $errline);
            $this->trace = $this->getTrace();
        } elseif (is_array($e)) {
            parent::__construct($e['message'], $e['type'], $e['type'], $e['file'], $e['line']);
            $this->trace = $this->getTrace();
        } elseif (is_object($e)) {
            parent::__construct($e->getMessage(), $e->getCode(), $e->getCode(), $e->getFile(), $e->getLine());
            $this->trace = $e->getTrace();
        } elseif (is_string($e)) {
            parent::__construct($e, $errstr);
            $this->trace = $this->getTrace();
        }
        $this->errorInfo[] = 'ErrorLevel {' . $this->getName() . '}';
        $this->errorInfo[] = array('message' => $this->getMessage(),
            'errorFile' => $this->getFile(),
            'errorLine' => $this->getLine(),
            'errorTrace' => $this->trace);
        return $this;
    }

    public function code() {
        return $this->getCode();
    }

    protected function isFatal($type) {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    public function show() {
        if ($this->isFatal($this->getCode()) || UOKE_DEBUG == true) {
            Log::writeLog($this->errorInfo);
            if (IS_CLI) {
                var_dump($this->errorInfo);
            } else {
                if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    var_dump($this->errorInfo);
                } else {
                    var_dump($this->errorInfo);
                }
            }
        }
    }

    public function __toString() {
		$echo = null;
		array_walk($this->errorInfo, function($value, $key) use(&$echo) {
			if(!isset($value['message'])) {
				$html = '<p>'.$value.'</p>';
			} else {
				$html = '<p>Error: '.$value['message'].'</p>';
				$html .= '<p>File Info: '.$value['errorFile'].'('.$value['errorLine'].')'.'</p>';
				$html .= '<pre>Trace: '.var_export($value['errorTrace'], true).'</pre>';
			}
			
			$echo .= $html;
		});
		return $echo;
    }

    private function getName() {
        static $names = [
            E_COMPILE_ERROR => 'PHP Compile Error',
            E_COMPILE_WARNING => 'PHP Compile Warning',
            E_CORE_ERROR => 'PHP Core Error',
            E_CORE_WARNING => 'PHP Core Warning',
            E_DEPRECATED => 'PHP Deprecated Warning',
            E_ERROR => 'PHP Fatal Error',
            E_NOTICE => 'PHP Notice',
            E_PARSE => 'PHP Parse Error',
            E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
            E_STRICT => 'PHP Strict Warning',
            E_USER_DEPRECATED => 'PHP User Deprecated Warning',
            E_USER_ERROR => 'PHP User Error',
            E_USER_NOTICE => 'PHP User Notice',
            E_USER_WARNING => 'PHP User Warning',
            E_WARNING => 'PHP Warning',
        ];
        return isset($names[$this->getCode()]) ? $names[$this->getCode()] : 'Error[' . $this->getCode() . ']';
    }

}
