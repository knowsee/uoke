<?php
namespace Action;
use Uoke\Controller, Services\IndexList;
class Index extends Controller{
    public function __construct() {}

    public function Index() {
        echo json_encode(IndexList::getMyList());
    }
}