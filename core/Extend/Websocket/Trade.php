<?php
namespace Websocket;
use Services\{Buy, Sell, TradeInfo};
class Trade extends MethodParse {
    private $lastId = 0;
    public function onCall($connectionId) {
        return $this->getWaitTrade();
    }
    
    public function onMessage() {
        return $this->getWaitTrade();
    }
    
    private function getWaitTrade() {
        if($this->lastId < 1) {
            list($total, $tradeList) = TradeInfo::getList(1, 50);
            $this->lastId = $tradeList[0];
        } else {
            list($total, $tradeList) = TradeInfo::getListByWhere(1, 50, array(
                'id' => array('>', $this->lastId)
            ));
            $this->lastId = $tradeList[0];
        }
        list($total, $buyList) = Buy::getList(1, 50);
        list($total, $sellList) = Sell::getList(1, 50);
        krsort($tradeList);
        return [
            'buyList' => $buyList,
            'sellList' => $sellList,
            'tradeList' => $tradeList
        ];
    }
}
