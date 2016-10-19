<?php
namespace Services;
use Factory\Db;
class IndexList {

    const TABLE_NAME = 'service';
    public static function getMyList() {
        /**
         * $listCount is this query return numRows
         */
        list($listCount, $list) = self::table()->where()->getList();
        foreach($list as $key => $value) {
            $tableList[] = $value;
        }
        return $tableList;
    }

    private static function table($table = self::TABLE_NAME) {
        return Db::getInstance()->table($table);
    }
}