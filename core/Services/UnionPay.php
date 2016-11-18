<?php
namespace Services;
use Factory\Db;

class UnionPay {

    const TABLE_NAME = 'unionPay';
    const POST_UNIONPAY_WAITING = 100;
    const POST_UNIONPAY_TRUE = 106;
    const POST_UNIONPAY_ERROR = 104;

    public static function createOrder($orderId, $orderPayCard, $orderPayRealId, $orderPayRealName, $orderMoney) {

        self::table()->insert(array(
            'orderId' => $orderId,
            'orderPayCard' => $orderPayCard,
            'orderPayRealId' => $orderPayRealId,
            'orderPayRealName' => $orderPayRealName,
            'orderMoney' => $orderMoney,
            'orderStatus' => self::POST_UNIONPAY_WAITING,
            'orderTime' => UNIXTIME
        ));

    }

    public static function updateStatusByOrderId($orderId, $status, $message = null) {
        self::updateOrderByOrderId($orderId, array('orderStatus' => $status, 'orderMessage' => $message));
    }

    public static function updateOrderByOrderId($orderId, $data) {
        self::table()->where(array(
            'orderId' => $orderId
        ))->update($data);
    }

    private static function table($table = self::TABLE_NAME) {
        return Db::getInstance()->table($table);
    }

}