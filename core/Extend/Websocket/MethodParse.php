<?php
namespace Websocket;
abstract class MethodParse {
    
    /**
     * Socket Call on Connect
     */
    abstract public function onCall($connectionId);
    
    /**
     * Socket Call on Message
     */
    abstract public function onMessage();
    
}
