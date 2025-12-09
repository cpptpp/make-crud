<?php

namespace Meioa\Tools\action;



use think\facade\Db;

class AddRow extends BaseAction
{
    private $_isUpdate = false;

    private function _isTimeColumn($columnName){
        return preg_match("/(time)$/", $columnName);
    }
    private function _isCreateTime($columnName){
        return preg_match("/(create_time)$/", $columnName);
    }

    private function _isUpdateTime($columnName){
        return preg_match("/(update_time)$/", $columnName);
    }
    private function _validData($data){

        $columns = (new GetColumns($this->table,$this->tablePk))->run();
        $insertData = [];
        foreach ($columns['data'] as $column){

            $columnName = $column['COLUMN_NAME'];
            if($this->_isTimeColumn($columnName) ){
                if( $this->_isCreateTime($columnName) ){
                    if(!$this->_isUpdate){
                        $insertData[$columnName] = time();
                    }
                }else if($this->_isUpdateTime($columnName)){
                    $insertData[$columnName] = time();
                }else if(isset($data[$columnName])){
                    $insertData[$columnName] = empty($data[$columnName])?0:strtotime($data[$columnName]);
                }
            }elseif(isset($data[$columnName])){
                $insertData[$columnName] = (new ParamConvert(__NAMESPACE__.'\\params\\type'))->run($column['DATA_TYPE'],$data[$columnName]);
            }
        }

        return $insertData;
    }

    public function run($data){

        //$this->checkIsUpdate($data);
        $this->getIsUpdate($data);
        $data = $this->_validData($data);
        if($this->_isUpdate){
            return $this->_update($data);
        }

        if(isset($data[$this->tablePk])){
            unset($data[$this->tablePk]);
        }

        $res = Db::table($this->table)->insertGetId($data);

        if($res<1){
            return Result::array(110,'添加失败！',$res);
        }else{
            return Result::array(0,'',$res);
        }
    }

    private function _update($data){
        $res = Db::table($this->table)->where([$this->tablePk=>$data[$this->tablePk]])->update($data);
        if($res<1){
            return Result::array(110,'更新失败！',$res);
        }else{
            return Result::array(0,'',$res);
        }
    }

    public function getIsUpdate($data){
        $this->_isUpdate = isset($data[$this->tablePk]) && $data[$this->tablePk]>0;
        return $this->_isUpdate;
    }

}
