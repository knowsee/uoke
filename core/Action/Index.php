<?php
namespace Action;
use Uoke\Controller, HongLing\Order;
class Index extends Controller{
    public function __construct() {}

    public function Index() {
        header("Content-type: text/xml");
        Order::createOrder();
        echo Order::getXml();
    }
}