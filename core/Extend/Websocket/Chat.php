<?php
namespace Websocket;
use morozovsk\websocket\Daemon;
class Chat extends Daemon {

    const VERSION = '0.0.5.60';
	private $user;

    protected function onOpen($connectionId, $info) {//it is called when new connection is open
        $this->sendToClient($connectionId, 'system<r>' . $connectionId . '<r>欢迎你的到来！目前在线：'.count($this->clients).'人； 版本：' . self::VERSION);
    }

    protected function onClose($connectionId) {//it is called when existed connection is closed
		unset($this->user[$connectionId]);
    }

    protected function onMessage($connectionId, $data, $type) {//it is called when a message is received from the client
        if (!strlen($data)) {
            return;
        }
        $get = json_decode(strip_tags($data), true);
        if (strlen(trim($get['user'])) < 3) {
            $get['user'] = '鬼Ghost' . $connectionId;
        }
        if(isset($get['user'])) {
            $get['user'] = trim($get['user']);
        }
		if($get['user'] && (array_search($get['user'], $this->user) == $connectionId || !array_search($get['user'], $this->user))) {
			$this->user[$connectionId] = $get['user'];
		} elseif($get['message'] !== '@login') {
			$this->sendToClient($connectionId, 'system<r>'.$connectionId.'<r>你用了一个别人在用的名字！将随机一个给你发');
		}
        if($get['message'] == '@login') {
            foreach ($this->clients as $clientId => $client) {
                $this->sendToClient($clientId, 'system<r>'.$clientId.'<r> 欢迎新人！【'.$this->user[$connectionId].'】');
            }
            return;
        }
        if($get['message'] == '@user') {
            $this->sendToClient($connectionId, 'system<r>'.$connectionId.'<r>'.implode(',', $this->user));
            return;
        }
        if ($get['action'] == 'startup') {
            $this->sendToClient($connectionId, '欢迎你的到来！现在可以开始聊天了');
        } else {
            if (!strlen($get['message'])) {
                return;
            }
            if($get['at'] > 0 && !isset($this->user[$get['at']])) {
                $this->sendToClient($connectionId, 'system<r>'.$connectionId.'<r>对方下线啦，你无法@他');
            }
            $message = strip_tags($get['message']);
            $message = '' . $this->user[$connectionId] . '<r>' . $connectionId . '<r>' . $message . ' (' . date('H:i:s', time()) . ')';
            $message = is_numeric($get['at']) > 0 ? $message . '<r>' . $this->user[$get['at']] : $message;
            if (is_numeric($get['at'])) {
                $this->sendToClient($get['at'], 'system<r>' . $this->user[$connectionId]);
            }
            foreach ($this->clients as $clientId => $client) {
                $this->sendToClient($clientId, $message);
            }
        }
    }

}
