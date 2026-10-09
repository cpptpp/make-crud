<?php

namespace Meioa\Tools;

use think\Facade;


class TableCache
{
    CONST TABLE_COLUMN_PREFIX = 'table_columns_';
    CONST TABLE_PK = 'table_pk_';

    private static $_tablePkArr = [];
    private $_store;
    public function __construct()
    {

        $this->_store = Facade::make(FileCache::class);
        $this->_store->setSubdir();
        $this->_store->setPrefix('table');
    }
    /**
     * 查找表主键
     * @param $table
     * @return mixed|string
     */
    public function getPk($table){
        if(!isset(self::$_tablePkArr[$table]) || empty(self::$_tablePkArr[$table])){
            self::$_tablePkArr[$table] = $this->_store->get(self::TABLE_PK.$table);
        }
        return self::$_tablePkArr[$table];
    }

    public  function getColumns($table){
        return $this->_store->get(self::TABLE_COLUMN_PREFIX.$table);
    }
    public  function setColumns($columns){
        foreach ($columns as $table =>$tableColumnList){
            $this->_store->set(self::TABLE_COLUMN_PREFIX.$table,$tableColumnList);
        }
    }

    public function setPks($pks){
        foreach ($pks as $table => $pk){
            $this->_store->set(self::TABLE_PK.$table,$pk);
        }
    }

    /**
     * 获取所有存在主键的表名称
     * @return array
     */
    public function getPkTables(){
        $tables = [];
        $len = strlen(self::TABLE_PK);
        foreach ($this->_store->getKeys() as $key){
            if (strpos($key, self::TABLE_PK) !== 0){
                continue;
            }
            $table = substr($key, $len);
            if ($table === '' || empty($this->getPk($table))){
                continue;
            }
            $tables[] = $table;
        }
        return $tables;
    }

}
