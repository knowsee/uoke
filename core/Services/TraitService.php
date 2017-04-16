<?php
namespace Services;
use Factory\Db;
/**
 * Trait Service
 * @author knowsee
 */
trait TraitService {
    /**
     * Get DataList
     * @param int $page
     * @param int $num
     * @param callable|null $callback
     * @param array $groupBy
     * @return array [totalNum, List]
     * @throws SError
     */
    public static function getList($page = 1, $num = 20, callable $callback = null, array $groupBy = array()) {
        if($page < 1 || $num < 1) {
            throw new \Services\SError(SError::DEFAULT_ERROR, '$page or $num is not num or less than 1', array(
                'page' => $page,
                'num' => $num
            ));
        }
        if($callback && !is_callable($callback)) {
            throw new \Services\SError(SError::IMPORTANT_ERROR, '$callback need is callable', array(
                'callableType' => gettype($callback),
            ));
        }
        list($offset, $row) = self::pageHandle($page, $num);
        return self::getDb()->limit($offset, $row)->order(self::TABLE_ORDER)->groupBy($groupBy)->getList($callback);
    }
    
    /**
     * getListByWhere
     * @param int $page
     * @param int $num
     * @param array $where
     * @param callable $callback
     * @param array $groupBy
     * @return array
     * @throws SError
     */
    public static function getListByWhere($page = 1, $num = 20, array $where = array(), callable $callback = null, array $groupBy = array()) {
        if($page < 1 || $num < 1) {
            throw new \Services\SError(SError::DEFAULT_ERROR, '$page or $num is not num or less than 1', array(
                'page' => $page,
                'num' => $num
            ));
        }
        if($callback && !is_callable($callback)) {
            throw new \Services\SError(SError::IMPORTANT_ERROR, '$callback need is callable', array(
                'callableType' => gettype($callback),
            ));
        }
        list($offset, $row) = self::pageHandle($page, $num);
        return self::getDb()->limit($offset, $row)->where($where)->order(self::TABLE_ORDER)->groupBy($groupBy)->getList($callback);
    }
    
    public static function pageHandle($page, $num) {
        return array(($page - 1)*$num, $num);
    }
    
    /**
     * getOne
     * @param array $order
     * @return array
     */
    public static function getOne($order = array()) {
        return self::getDb()->order($order)->getOne();
    }
    
    /**
     * getCount
     * @return int
     */
    public static function getCount() {
        return self::getDb()->getCount();
    }
    
    /**
     * Insert To Db
     * @param array $data
     * @param bool $lastId
     * @return mixed
     */
    public static function Insert(array $data, bool $lastId = false) {
        return self::getDb()->insert($data, $lastId);
    }
    
    /**
     * Update
     * @param array $data
     * @param array $where
     * @return result
     */
    public static function Update(array $data, array $where = array()) {
        return self::getDb()->where($where)->update($data);
    }
    
    /**
     * UpdateById
     * @param mixed $id
     * @param array $data
     * @return result
     */
    public static function UpdateById($id, array $data) {
        return self::getDb()->updateById($id, $data);
    }
    
    /**
     * Delete
     * @param array $where
     * @return result
     */
    public static function Delete(array $where = array()) {
        return self::getDb()->where($where)->delete();
    }
    
    /**
     * DeleteById
     * @param mixed $id
     * @return result
     */
    public static function DeleteById($id) {
        return self::getDb()->deleteById($id);
    }
    
    /**
     * distinctCount
     * @param string $feild
     * @return int
     */
    public static function distinctCount(string $feild) {
        return self::getDb()->feild(array($feild => Db::FIELD_DISTINCT))->getCount();
    }
    
    /**
     * getById
     * @param mixed $id
     * @return array
     */
    public static function getById($id) {
        return self::getDb()->getById($id);
    }
    
    /**
     * getByWhere
     * @param array $where
     * @param array $order
     * @return array
     */
    public static function getByWhere(array $where, $order = array()) {
        return self::getDb()->where($where)->order($order)->getOne();
    }
    
    /**
     * getDb
     * @return Db
     */
    public static function getDb() {
        return Db::getInstance(array(
                    'pkey' => self::TABLE_PY,
                ))->table(self::TABLE_NAME);
    }
}
