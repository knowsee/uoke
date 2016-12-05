<?php
namespace DbExtend;
use Adapter\Db, Helper\Log, Uoke\uError;

/**
 * Mysqli数据库引擎适配器
 * @author Knowsee
 */
class Mysqli implements Db {

    private $link = NULL;
    private $config = array();
    private $sqlTable = NULL;
    private $isTransaction = false;
    private $isAutoTransaction = false;
    private $sqlAction = array(
        'where' => '',
        'groupby' => '',
        'having' => '',
        'limit' => '',
        'order' => '',
    );
    private $sqlExtArray = array(
        'where' => '',
        'groupby' => '',
        'having' => '',
        'limit' => '',
        'order' => '',
    );
    private $queryId = array();

    public function __construct($config) {

        if (empty($this->config)) {
            $this->config = $config;
        }
        if (!$this->link) {
            $this->link = new \mysqli($this->config['host'], $this->config['user'], $this->config['password'], $this->config['name']);
            try {
                if ($this->link->connect_errno) {
                    throw new uError('Mysql Host Can\'t Connect', $this->link->connect_errno);
                } else {
                    $this->link->set_charset($this->config['charset']);
                }
            } catch (uError $e) {
                var_dump($e->getMessage());
                exit();
            }
        }
        return $this;
    }

    public function table($tableName) {
        $this->sqlTable = '`' . $this->config['pre'] . $tableName . '`';
        return $this;
    }

    public function getOne() {
        $sql = sprintf('SELECT %s FROM %s WHERE %s', $this->sqlExtArray['feild'], $this->sqlTable, $this->sqlExtArray['where']);
        return $this->query($sql)->fetch_assoc();
    }

    public function getList() {
        $sql = sprintf('SELECT %s FROM %s WHERE %s %s %s %s %s', $this->sqlExtArray['feild'], $this->sqlTable, $this->sqlExtArray['where'], $this->sqlExtArray['order'], $this->sqlExtArray['groupby'], $this->sqlExtArray['having'], $this->sqlExtArray['limit']);
        if ($this->queryId) {
            $this->numCols = $this->numRows = 0;
            $this->queryId = null;
        }
        $this->queryId = $this->query($sql);
        if ($this->link->more_results()) {
            while (($res = $this->link->next_result()) != NULL) {
                $res->free_result();
            }
        }
        if (false ===! $this->queryId) {
            $this->numRows = $this->queryId->num_rows;
            $this->numCols = $this->queryId->field_count;
            return array($this->numRows, $this->getAll());
        }
    }
    
    private function getAll() {
        $result = array();
        if ($this->numRows > 0) {
            for ($i = 0; $i < $this->numRows; $i++) {
                $fetchArray = $this->queryId->fetch_assoc();
                $result[$i] = $fetchArray;
            }
            $this->queryId->data_seek(0);
        }
        return $result;
    }

    public function getInsertLastId() {
        return $this->link->insert_id;
    }

    public function getField() {
        $sql = sprintf('SELECT %s FROM %s WHERE %s', $this->sqlExtArray['feild'], $this->sqlTable, $this->sqlExtArray['where']);
        return $this->query($sql)->fetch_assoc();
    }

    public function getOneField() {
        $sql = sprintf('SELECT %s FROM %s WHERE %s', $this->sqlExtArray['feild'], $this->sqlTable, $this->sqlExtArray['where']);
        $row = $this->query($sql)->fetch_row();
        return $row[0];
    }

    public function getVersion() {
        return $this->link ? $this->link->server_version : '0.0';
    }

    public function insert($data, $return_insert_id = false, $replace = false) {
        $sql = sprintf('%s %s SET %s', $replace ? 'REPLACE INTO' : 'INSERT INTO', $this->sqlTable, $this->arrayToSql($data));
        $return = $this->query($sql);
        return $return_insert_id ? $this->getInsertLastId() : $return;
    }

    public function insertReplace($data, $affected = false) {
        $sql = sprintf('INSERT IGNORE INTO %s SET %s', $this->sqlTable, $this->arrayToSql($data));
        $return = $this->query($sql);
        return $affected ? $this->link->affected_rows : $return;
    }

    public function insertMulti($key, $data, $replace = false) {
        foreach ($key as $k => $value) {
            $fkey[] = "`$value`";
        }
        $sql = '(' . implode(',', $fkey) . ')';
        $sql = $sql . ' VALUES ';
        foreach ($data as $k => $value) {
            $ky = array();
            foreach ($value as $vk => $vvalue) {
                $ky[] = "'$vvalue'";
            }
            $kkey[$k] = '(' . implode(',', $ky) . ')';
        }
        $data = $sql . implode(',', $kkey);
        $sql = sprintf('%s %s SET %s', $replace ? 'REPLACE INTO' : 'INSERT INTO', $this->sqlTable, $data);
        return $this->query($sql);
    }

    public function update($data, $longWait = false) {
        $sql = sprintf('%s %s SET %s WHERE %s', 'UPDATE' . ($longWait ? 'LOW_PRIORITY' : ''), $this->sqlTable, $this->arrayToSql($data), $this->sqlExtArray['where']);
        return $this->query($sql);
    }

    public function delete() {
        $sql = sprintf('DELETE FROM %s WHERE %s', $this->sqlTable, $this->sqlExtArray['where']);
        return $this->query($sql);
    }

    public function query($sql) {
        $debug['sql'] = $sql;
        $debug['begin'] = microtime(true);
        try {
            $result = $this->link->query($sql);
            if ($this->link->error) {
                throw new uError('Mysql('.$this->getVersion().')'.$this->link->error, $this->link->errno);
            }
            $debug['end'] = microtime(true);
            $debug['time'] = '[ RunTime:' . floatval($debug['end'] - $debug['begin']) . 's ]';
            if (is_object($this->link->query("explain $sql")))
                $debug['debugSql'] = $this->link->query("explain $sql")->fetch_assoc();
            Log::writeLog($debug, 'sql');
        } catch (uError $e) {
            return false;
        }
        return $result;
    }

    public function beginTransaction($flag = null) {
        $trans = $this->link->begin_transaction();
        if($trans == true) {
            $this->isTransaction = true;
        } else {
            throw new uError('Transaction can not open');
        }
    }
    public function autocommitTransaction() {
        if($this->isTransaction = false) {
            throw new uError('Transaction is not open');
        } else {
            if($this->isAutoTransaction == false) {
                $this->link->autocommit(true);
                $this->isAutoTransaction = true;
            } else {
                $this->link->autocommit(false);
                $this->isAutoTransaction = false;
            }
        }
    }
    public function rollbackTransaction() {
        if($this->isTransaction = true) {
            $this->link->rollback();
            $this->isTransaction = false;
        } else {
            throw new uError('Transaction is not open');
        }
    }
    public function commitTransaction() {
        if($this->isTransaction = true) {
            $this->link->commit();
            $this->isTransaction = false;
        } else {
            throw new uError('Transaction is not open');
        }
    }

    public function handleSqlFunction($sqlTable, $sqlArray) {
        $this->table($sqlTable);
        foreach($sqlArray as $key => $value) {
            switch($key) {
                case 'order':
                $this->order($value);
                    break;
                case 'where':
                    $this->where($value);
                    break;
                case 'limit':
                    $this->limit($value);
                    break;
                case 'or':
                    $this->whereOr($value);
                    break;
                case 'group':
                    $this->groupBy($value);
                    break;
                case 'having':
                    $this->havingBy($value);
                    break;
                case 'feild':
                    $this->feild($value);
                    break;
            }
        }
        $this->handleEasySql();
    }

    private function escape($sqlValue) {
        return $this->link->real_escape_string($sqlValue);
    }

    public function fieldType($fieldList) {
        foreach($fieldList as $field => $fieldDo) {
            $sqlField[] = sprintf('%s(%s) %s', $fieldDo, $field, $field=='*'?'':$field);
        }
        return implode(', ', $sqlField);
    }

    /**
     * @title array to sql
     * @param $array
     * EG: sql: WHERE KEY > '1' AND KEY < '10'
     *     php: $handleThis->where(array('KEY' => array('>' => '1', '<=' => '10')))
     *     sql: WHERE KEY LIKE %'1'%
     *     php: $handleThis->where(array('KEY' => array('LIKEMORE' => '1')));
     *     sql: WHERE KEY IN('1','2','3','4','5')
     *     php: $handleThis->where(array('KEY'=> array('IN' => array(1,2,3,4,5))))
     * @return array
     */
    public function handleSql($array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $handle => $val) {
                    $sql[] = self::condSql($key, $this->escape($val), $handle);
                }
            } else {
                $sql[] = self::condSql($key, $this->escape($value), '');
            }
        }
        return $sql;
    }

    public function condSql($key, $value, $handle) {
        if (in_array($handle, array('>', '<', '>=', '<=', '!=', '<>'))) {
            $sql = "`$key` " . $handle . " '$value'";
        } elseif ($handle == 'IN') {
            $sql = "`$key` IN(".  dimplode($value).")";
        } elseif ($handle == 'LIKE') {
            $sql = "`$key` LIKE '$value'";
        } elseif ($handle == 'LIKEMORE') {
            $sql = "`$key` LIKE '%$value%'";
        } else {
            $sql = "`$key` = '$value'";
        }
        return $sql;
    }

    public function arrayToSql($array, $glue = ',') {
        $sql = $comma = '';
        foreach ($array as $k => $v) {
            $k = trim($k);
            if(is_array($v)) {
                $sql[] = "`$k`= `$k`$v[0]'$v[1]'";
            } else {
                $sql[] = "`$k`='$v'";
            }
        }
        return implode($glue, $sql);
    }

    private function order($array) {
        if (!is_array($array) || !$array)
            return '';
        foreach ($array as $key => $value) {
            $order[] = "$key $value";
        }
        $this->sqlAction['order'] = $order ? 'ORDER BY ' . implode(',', $order) : '';
        return $this;
    }

    private function where($array) {
        $this->sqlAction['where'][] = implode(' AND ', $this->handleSql($array));
        return $this;
    }

    private function limit($array) {
        if (!is_array($array) || !$array)
            return '';
        $this->sqlAction['limit'] = 'LIMIT ' . $array[0] . ',' . $array[1];
        return $this;
    }

    private function whereOr($array) {
        $this->sqlAction['where'][] = '(' . implode(' OR ', $this->handleSql($array)) . ')';
        return $this;
    }

    private function groupBy($array) {
        $this->sqlAction['groupby'] = $array ? 'GROUP BY ' . implode(',', $array) : '';
        return $this;
    }

    private function havingBy($array) {
        $this->sqlAction['having'] = 'HAVING ' . $this->handleSql($array);
        return $this;
    }

    private function feild($array) {
        $this->sqlAction['feild'] = $this->fieldType($array);
        return $this;
    }

    private function handleEasySql() {
        $this->sqlExtArray = array(
            'feild' => $this->sqlAction['feild'] ? $this->sqlAction['feild'] : '*',
            'where' => $this->sqlAction['where'] ? implode(' AND ', $this->sqlAction['where']) : '1',
            'groupby' => $this->sqlAction['groupby'],
            'having' => $this->sqlAction['having'],
            'limit' => $this->sqlAction['limit'],
            'order' => $this->sqlAction['order']
        );
        $this->sqlAction = array(
            'where' => '',
            'groupby' => '',
            'having' => '',
            'limit' => '',
            'order' => '',
            'feild' => ''
        );
    }

}
