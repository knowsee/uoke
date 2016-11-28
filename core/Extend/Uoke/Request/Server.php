<?php
namespace Uoke\Request;
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}
/**
 * Request/Server Uoke Extend
 *
 * Some Function copy from Yii
 * @desc Server can get serverInfo, only get local server info
 * @package Uoke\Request
 */
class Server {
    private static $instance = null;

    public static function getInstance() : Client {
        if(!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}