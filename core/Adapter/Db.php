<?php
namespace Adapter;
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}
/**
 * 数据库适配器接口
 * @author Knowsee
 */
interface Db {
    
    public function table($tableName);
    public function getOne();
    public function getList();
    public function getInsetLastId();
    public function getFieldAny($field);
    public function getFieldCount($field, $countType);
    public function getVersion();
    public function insert($data, $return_insert_id = false, $replace = false);
    public function insertReplace($data, $affected = false);
    public function insertMulti($key, $data, $replace = false);
    public function update($data, $longWait = false);
    public function delete();
    public function query($sql);
    public function handleSqlFunction($sqlTable, $sqlArray);

}
