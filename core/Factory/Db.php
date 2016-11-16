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
    /**
     * @var \Adapter\Db
     */
    private $dbLink = NULL;
    private $sqlTable = NULL;
    private $sqlAction = array();

    /**
     *
     * @param array $tableConfig
     * @return Db
     */
    public static function getInstance(array $tableConfig = array()) : Db {
        $key = to_guid_string($tableConfig);
        if (!is_object(self::$instance[$key])) {
            self::$instance[$key] = new self($tableConfig);
        }
        return self::$instance[$key];
    }

    public function __construct(array $tableConfig = array())
    {
        $this->tablePK = isset($tableConfig['pkey']) ? $tableConfig['pkey'] : 'id';
        $this->tableCacheTime = isset($tableConfig['cacheTime']) ? $tableConfig['cacheTime'] : 600;
        if(!$this->dbLink) {
            $dbType = '\\DbExtend\\'.ucwords(CONFIG('db/type'));
            $this->dbLink = new $dbType(CONFIG('db'));
        }
        return $this;
    }

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

    public function getOne() : array {
        $returnArray = $this->runDb()->getOne();
        return (array)$returnArray;
    }

    public function getById(string $id) : array {
        $this->where(array($this->tablePK => $id));
        $return = $this->runDb()->getOne();
        return (array)$return;
    }

    public function insert(array $data, bool $return_insert_id = false, bool $replace = false) : int {
		if(!is_array($data) || !$data)
		    return 0;
        $returnArray = $this->runDb()->insert($data, $return_insert_id, $replace);
        return (int)$returnArray;
    }

    public function update(array $data, bool $longWait = false) {
		if(!is_array($data) || !$data) return false;
        return $this->runDb()->update($data, $longWait);
    }

    public function updateById(int $id, array $data, bool $longWait = false) {
        $this->where(array($this->tablePK => $id));
        return $this->runDb()->update($data, $longWait);
    }

    public function delete() {
        return $this->runDb()->delete();
    }

    public function deleteById(int $id) {
        $this->where(array($this->tablePK => $id));
        return $this->runDb()->delete();
    }

    public function table(string $tableName) : Db  {
        $this->sqlTable = $tableName;
        return $this;
    }

    public function order(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['order'] = $array;
        return $this;
    }

    public function where(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['where'] = $array;
        return $this;
    }

    public function limit(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['limit'] = $array;
        return $this;
    }

    public function whereOr(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['or'] = $array;
        return $this;
    }

    public function groupBy(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['group'] = $array;
        return $this;
    }

    public function havingBy(array $array = array()) : Db {
        if (!is_array($array) || !$array)
            return $this;
        $this->sqlAction['having'] = $array;
        return $this;
    }

    public function beginTrans() {
        $this->dbLink->beginTransaction();
    }

    public function autoTrans() {
        $this->dbLink->autocommitTransaction();
    }

    public function commitTrans() {
        $this->dbLink->commitTransaction();
    }

    public function rollbackTrans() {
        $this->dbLink->rollbackTransaction();
    }

    private function runDb() {
        $this->dbLink->handleSqlFunction($this->sqlTable, $this->sqlAction);
        return $this->dbLink;
    }


}
