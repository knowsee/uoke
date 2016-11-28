<?php
namespace Uoke;
/**
 * 异常处理与分析类
 *
 * @author chengyi
 */
class uError extends \ErrorException {

    public function __construct($e, $errstr = '', $errfile = __FILE__, $errline = __LINE__) {
        $trace = '';
        if (is_numeric($e)) {
            parent::__construct($errstr, $e, $e, $errfile, $errline);
            $trace = $this->getTrace();
        } elseif(is_array($e)) {
            parent::__construct($e['message'], $e['type'], $e['type'], $e['file'], $e['line']);
            $trace = $this->getTrace();
        } elseif(is_object($e)) {
            parent::__construct($e->getMessage(), $e->getCode(), $e->getCode(), $e->getFile(), $e->getLine());
            $trace = $e->getTrace();
        }
        if (IS_CLI) {
            http_response_code(500);
        }
        if(UOKE_DEBUG) {
            $message[] = 'ErrorLevel {'.$this->getName().'}';
            $message[] = array('message' => $this->getMessage(),
                'errorFile' => $this->getFile(),
                'errorLine' => $this->getLine(),
                'errorTrace' => $trace);
            echo '<script>console.log('.json_encode($message, JSON_UNESCAPED_UNICODE).')</script>';
            error_log(var_export($message, true));
            exit('Uoke back to the  ['.$this->getName().'] door');
        }
    }

    public function getName() {
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
        return isset($names[$this->getCode()]) ? $names[$this->getCode()] : 'Error['.$this->getCode().']';
    }

    private static function inFile($file) {
        return stripslashes($file);
    }

}