<?php
namespace Action;
use Factory\Db, Uoke\Controller;
class Index extends Controller{

    public function __construct($m, $t) {
        echo $m.$t;
    }

    public function Index() {
        var_dump(array('duing'));
    }
    
    private function db() {
        return Db::getInstance(array('ukey' => 'ip'));
    }
}