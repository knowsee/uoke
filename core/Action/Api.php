<?php
namespace Action;
use Uoke\Controller, HongLing\Order, Services\HomlinOrder, UnionPay\RemitPayment, Services\UnionPay;

class Api extends Controller{

    public function __construct() {}

    public function hl() {
        $do = $this->client->get('do');
        if($do == 'create') {
            $this->doCreate();
        } elseif($do == 'query') {
            $this->doQuery();
        } elseif($do == 'createTest') {
            $this->doTestC();
        } elseif($do == 'unionPay') {
            $this->doUnionPay();
        } elseif($do == 'queryMianLiao') {
            $this->mianliao();
        }
    }

    private function mianliao() {
        $e = Order::queryFabric($this->client->get('queryFabric'));
        $this->rightWithJson($e);
    }

    private function doUnionPay() {
        $apiPost = $this->client->post();
        $orderArray = RemitPayment::payTo($apiPost['cardId'], $apiPost['Id'], $apiPost['realName'], $apiPost['money']);
        if(isset($orderArray['orderId'])) {
            UnionPay::createOrder($orderArray['orderId'], $apiPost['cardId'], $apiPost['Id'], $apiPost['realName'], $apiPost['money']);
            if(isset($orderArray['errorMsg'])) {
                $this->errorWithJson(array('apiError' => true), $orderArray['errorMsg']);
            } else {
                $this->rightWithJson(array('orderId' => $orderArray['orderId']), '已经发送去银联接口');
            }
        } else {
            $this->errorWithJson(array('apiError' => true), '答应失败');
        }

    }

    private function doTestC() {
        Order::openDebug(Order::DEBUG_TO_LOG);
        $apiPost = $this->client->post();
        $rInfo = Order::createOrder($apiPost);
        if($rInfo['orderPass'] == true) {
            HomlinOrder::createOrder($rInfo['orderId'], $apiPost);
            $this->rightWithJson(array('orderId' => $rInfo['orderId'], 'jh' => $rInfo), '订单已经保存下单');
        } else {
            $this->errorWithJson(array('orderId' => null), $rInfo['orderMsg']);
        }
    }

    private function doCreate() {
        $apiPost = $this->client->post();
        $rInfo = Order::createOrder($apiPost);
        if($rInfo['orderPass'] == true) {
            HomlinOrder::createOrder($rInfo['orderId'], $apiPost);
            HomlinOrder::updateOrderByOrderId($rInfo['orderId'], array('orderPostTime' => strtotime($rInfo['orderJh'])));
            $this->rightWithJson(array(
                'orderId' => $rInfo['orderId'],
                'orderStatus' => 1001,
                'orderTime' => strtotime('today'),
                'orderPostTime' => strtotime($rInfo['orderJh']),
            ));
        } else {
            $this->errorWithJson(array('orderId' => null), $rInfo['orderMsg']);
        }
    }

    private function doQuery() {
        $apiPost = $this->client->get();
        $orderInfo = Order::queryOrder($apiPost['orderId']);
        $this->rightWithJson(array(
            'kuaidi' => $orderInfo['kuaidi'],
        ));
    }
}