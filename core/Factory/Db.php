<?php declare(strict_types = 1);
namespace Factory;
/**
 * 数据库工厂
 * @author Knowsee
 */
class Db {

    private static $instance;
    private $tablePK = '';
    private $tableCacheTime = 0;
    private $dbLink = NULL;
    private $sqlTable = NULL;
    private $sqlAction = array();

    const FIELD_COUNT = 'COUNT';
    const FIELD_SUM = 'SUM';
    const FIELD_AVG = 'AVG';
    const FIELD_DISTINCT = 'DISTINCT';


    /**
     *
     * @param array $tableConfig = [pkey => '', 'cacheTime' => '']
     * cacheTime is not support
     * @return Db
     */
    public static function getInstance(array $tableConfig = array()) : Db {
        $key = to_guid_string($tableConfig);
        if (!is_object(self::$instance[$key])) {
            self::$instance[$key] = new self($tableConfig);
        }
        return self::$instance[$key];
    }

    public function __construct(array $tableConfig = array()) {
        $this->tablePK = isset($tableConfig['pkey']) ? $tableConfig['pkey'] : 'id';
        $this->tableCacheTime = isset($tableConfig['cacheTime']) ? $tableConfig['cacheTime'] : 600;
        if(!$this->dbLink) {
            $dbConfig = CONFIG();
            $dbClass = $dbConfig['dbDriver'][$dbConfig['db']['driver']];
            $this->dbLink = new $dbClass(CONFIG('db'));
        }
        return $this;
    }

    /**
     * Get list with sql
     * @param string $key
     * is field name
     * @param string $returnType
     * @return array
     */
    public function getList(string $key = '', string $returnType = 'string') : array {
        $returnArray = $this->runDb()->getList();
        if($key) {
            foreach($returnArray[1] as $value) {
                if($returnType == 'string') {
                    $returnList[$value[$key]] = $value;
                } else {
                    $returnList[$value[$key]][] = $value;
                }
            }
            $returnArray[1] = $returnList;
        }
        return (array)$returnArray;
    }

    /**
     * Fetch One
     * @return array
     */
    public function getOne() : array {
        $returnArray = $this->runDb()->getOne();
        return (array)$returnArray;
    }

    /**
     * Fetch One with pk
     * @param string $id
     * id need be use 'pkey' field
     * @return array
     */
    public function getById(string $id) : array {
        $this->where(array($this->tablePK => $id));
        $return = $this->runDb()->getOne();
        return (array)$return;
    }

    /**
     * Get lastInsert Id
     * @return mixed
     */
    public function getInsertLastId() {
        return $this->runDb()->getInsertLastId();
    }

    /**
     * Get One Sql With Field
     * @param $field
     * @return mixed
     */
    public function getFieldAny($field) {
        return $this->runDb()->getFieldAny($field);
    }

    /**
     * Get Field Count
     * @param $field
     * @return mixed
     */
    public function getOneField($field) {
        return $this->runDb()->getOneField($field);
    }

    /**
     * Get Sql Server Version
     * @return mixed
     */
    public function getVersion() {
        return $this->runDb()->getVersion();
    }

    /**
     * Insert data to sql
     * @param array $data
     * @param bool $return_insert_id
     * @param bool $replace
     * @return int
     */
    public function insert(array $data, bool $return_insert_id = false, bool $replace = false) : int {
		if(!is_array($data) || !$data)
		    return 0;
        $returnArray = $this->runDb()->insert($data, $return_insert_id, $replace);
        return (int)$returnArray;
    }

    /**
     * Update data to sql
     * @param array $data
     * @param bool $longWait
     * @return bool
     */
    public function update(array $data, bool $longWait = false) {
		if(!is_array($data) || !$data) return false;
        return $this->runDb()->update($data, $longWait);
    }

    /**
     * Update Data to sql with pk
     * @param int $id
     * id need be use 'pkey' field
     * @param array $data
     * @param bool $longWait
     * @return mixed
     */
    public function updateById(int $id, array $data, bool $longWait = false) {
        $this->where(array($this->tablePK => $id));
        return $this->runDb()->update($data, $longWait);
    }

    /**
     * Sql Delete
     * @return mixed
     */
    public function delete() {
        return $this->runDb()->delete();
    }

    /**
     * @param int $id
     * id need be use 'pkey' field
     * @return mixed
     */
    public function deleteById(int $id) {
        $this->where(array($this->tablePK => $id));
        return $this->runDb()->delete();
    }

    /**
     * @param string $tableName
     * @return Db
     */
    public function table(string $tableName) : Db  {
        $this->sqlTable = $tableName;
        return $this;
    }

    /**
     * @param array $array
     * $array['FIELD'] = ORDER_TYPE
     * @return mixed
     */
    public function order(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['order'] = $array;
        return $this;
    }

    /**
     * @param array $array
     * $array['FIELD'] = VALUE
     * @return mixed
     */
    public function where(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['where'] = $array;
        return $this;
    }

    /**
     * @param array $array
     * $array = [START, LIMIT_NUM]
     * @return mixed
     */
    public function limit(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['limit'] = $array;
        return $this;
    }

    /**
     * @param array $array
     * $array['FIELD'] = VALUE
     * @return mixed
     */
    public function whereOr(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['or'] = $array;
        return $this;
    }

    /**
     * @param array $array
     * $array = [GROUPBY_FIELD1, 2, 3, 4]
     * @return mixed
     */
    public function groupBy(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['group'] = $array;
        return $this;
    }

    /**
     * @param array $array
     * ED like where , you can read Uoke\Mysqli\handleSql
     * @return Db
     */
    public function havingBy(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['having'] = $array;
        return $this;
    }

    /**
     * open Trans
     */
    public function beginTrans() {
        $this->dbLink->beginTransaction();
    }

    /**
     * open auto Trans
     */
    public function autoTrans() {
        $this->dbLink->autocommitTransaction();
    }

    /**
     * commit sql to sql
     */
    public function commitTrans() {
        $this->dbLink->commitTransaction();
    }

    /**
     * rollback sql
     */
    public function rollbackTrans() {
        $this->dbLink->rollbackTransaction();
    }

    /**
     * @return \Adapter\Db
     */
    private function runDb() {
        $this->dbLink->handleSqlFunction($this->sqlTable, $this->sqlAction);
        return $this->dbLink;
    }


}
