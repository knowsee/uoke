<?php
namespace Services;
use Factory\Db;

class HomlinOrder {

    const TABLE_NAME = 'hlOrder';
    const ORDER_STATUS_WAITPOST = 101;
    const ORDER_STATUS_FACTORY_MAKING = 102;
    const ORDER_STATUS_FACTORY_MADE = 103;
    const ORDER_STATUS_FINISH = 104;

    public static function getOrderById($orderId) {
        return self::table()->where(array('orderId' => $orderId))->getOne();
    }

    public static function createOrder($orderId, $orderInfo, $orderStatus = self::ORDER_STATUS_WAITPOST) {

        self::table()->insert(array(
            'orderId' => $orderId,
            'orderInfo' => strpack($orderInfo),
            'orderClientName' => $orderInfo['clientName'],
            'orderClientMobile' => $orderInfo['clientMobile'],
            'orderStatus' => $orderStatus,
            'orderPostTime' => UNIXTIME
        ));

    }

    public static function updateStatusByOrderId($orderId, $status) {
        self::updateOrderByOrderId($orderId, array('orderStatus' => $status));
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