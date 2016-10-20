<?php
namespace Action;
use Uoke\Controller, Services\IndexList;
class Index extends Controller{
    public function __construct() {}

    public function Index() {
        $a = json_encode(IndexList::getMyList());
        $this->view('a', $a);
        $this->display('test');
    }
}