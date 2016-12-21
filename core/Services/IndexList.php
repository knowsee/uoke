<?php
namespace Services;
use Factory\Db;
class IndexList {

    const TABLE_NAME = 'service';


    public static function getMyList() {
        /**
         * $listCount is this query return numRows
         */
        list($listCount, $list) = self::table()->order(array('KEY' => 'VALUE'))->where(array('KEY' => 'VALUE'))->getList();
        foreach($list as $key => $value) {
            $tableList[] = $value;
        }

        return $tableList;
    }


    /*
     * get one row in table
     */
    public static function getOne($where) {
        self::table()->where($where)->getOne();
    }

    public static function getById($id) {
        self::table()->getById($id);
    }

    /*
     * same as delete, deleteById
     */
    public static function updateById($id, $data) {
        self::table()->updateById($id, $data);
    }

    public static function update($where, $data) {
        self::table()->where($where)->update($data);
    }

    private static function table($table = self::TABLE_NAME) {
        /*
         * array can be set pkey
         */
        return Db::getInstance(array(
            'pkey' => 'Table_ID' //AUTO_INCREMENT
        ))->table($table);
    }

}