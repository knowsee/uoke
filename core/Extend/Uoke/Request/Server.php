<?php
namespace Uoke\Request;
/**
 * Request/Server Uoke Extend
 *
 * Some Function copy from Yii
 * @desc Server can get serverInfo, only get local server info
 * @package Uoke\Request
 */
class Server {
    private static $instance = null;

    public static function getInstance() : Server {
        if(!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getRunType() {
        return IS_CLI ? 'cli' : 'client';
    }

    public function getClientConStatus() {
        $connectStatus = connection_status();
        if($connectStatus == 2) {
            return 'timeout';
        } elseif($connectStatus == 1) {
            return 'aborted';
        } else {
            return 'normal';
        }
    }

    public function isClientAborted() {
        return connection_aborted() == 1 ? true : false;
    }

    public function openIgnoreDisCon() {
        ignore_user_abort(true);
        return true;
    }

    public function closeIgnoreDisCon() {
        ignore_user_abort(false);
        return false;
    }

    /*
     * if $time > 0 is second time
     * if $time > 0 && < 1 is micro time
     * @param $time
     */
    public function setSleep(int $time) {
        if($time > 1) {
            sleep($time);
        } elseif($time > 0 && $time < 1) {
            usleep($time);
        } else {
            new Exception('Time is not real time', E_PARSE);
        }
    }

    public function setTimeLimit($second) {
        set_time_limit($second);
    }


}