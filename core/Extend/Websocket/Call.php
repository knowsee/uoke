<?php
namespace Websocket;
use morozovsk\websocket\Daemon, Services\ClientCall;
class Call extends Daemon {
    private $UserTrade = array();

    protected function onOpen($connectionId, $info) {
        $message = $this->Service('onCall', $connectionId);
        $this->sendToClient($connectionId, $message);
    }

    protected function onClose($connectionId) {
        ClientCall::Delete(array(
                'ClientId' => $connectionId
        ));
    }

    protected function onMessage($connectionId, $data, $type) {
        if (!strlen($data)) {
            return;
        }
        $getData = json_decode($data, true);
        if($getData['cmd'] == 'login') {
            ClientCall::Insert(array(
                'ClientId' => $connectionId,
                'UserId' => $getData['userId']
            ));
        } elseif($getData['cmd'] == 'radioToAll') {
            $message = $this->Service('onMessage', $connectionId);
            foreach ($this->clients as $clientId => $client) {
                $this->sendToClient($clientId, $message);
            }
        } elseif($getData['cmd'] == 'radioToUser') {
            
        }
    }

    private function Service($onType, $connectionId = '') {
        $UserTrade = new Trade();
        $message = $UserTrade->$onType($connectionId);
        return json_encode($message);
    }

}
