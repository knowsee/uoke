<?php
namespace Action;
use morozovsk\websocket\Server;
class Cli {
    public function Run() {
        global $argv;
        $config = array(
            'class' => '\Websocket\Chat',
            'pid' => MAIN_PATH . '/Data/websocket_chat.pid',
            'websocket' => 'tcp://0.0.0.0:89',
            'eventDriver' => 'event'
        );
        if (empty($argv[1]) || !in_array($argv[1], array('start', 'stop', 'restart'))) {
            $argv[1] = 'start';
            //die("need parameter (start|stop|restart)\r\n");
        }
        try {
            $WebsocketServer = new Server($config);
            call_user_func(array($WebsocketServer, $argv[1]));
        } catch (\ErrorException $e) {
            echo $e->getMessage();
        }
    }
    
    public function Call() {
        global $argv;
        $config = array(
            'class' => '\Websocket\Call',
            'pid' => MAIN_PATH . '/Data/websocket_call.pid',
            'websocket' => 'tcp://0.0.0.0:82',
            'eventDriver' => 'event'
        );
        if (empty($argv[1]) || !in_array($argv[1], array('start', 'stop', 'restart'))) {
            $argv[1] = 'start';
            //die("need parameter (start|stop|restart)\r\n");
        }
        try {
            $WebsocketServer = new Server($config);
            call_user_func(array($WebsocketServer, $argv[1]));
        } catch (\ErrorException $e) {
            echo $e->getMessage();
        }
    }
}
