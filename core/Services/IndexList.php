<?php
namespace Services;
use Factory\Db;
class IndexList {

    const TABLE_NAME = 'dailyTable';

    public static function getList() {
        return self::table()->limit('10,1')->order(array('id' => 'DESC'))->getList();
    }

    private static function table($table = self::TABLE_NAME) {
        /*
         * array can be set pkey
         */
        return Db::getInstance(array(
            'pkey' => 'id' //AUTO_INCREMENT
        ))->table($table);
    }

}