<?php

namespace Meioa\Tools\action;

class ParamAnalyse
{

    public function isTimeColumn($columnName){
        return preg_match("/(time)$/", $columnName);
    }
    public function isCreateTime($columnName){
        return preg_match("/(create_time)$/", $columnName);
    }

    public function isUpdateTime($columnName){
        return preg_match("/(update_time)$/", $columnName);
    }

    public function validData($data,$columns,$isUpdate): array
    {


        $insertData = [];
        foreach ($columns['data'] as $column){

            $columnName = $column['COLUMN_NAME'];
            if($this->isTimeColumn($columnName) ){
                if( $this->isCreateTime($columnName) ){
                    if(!$isUpdate){
                        $insertData[$columnName] = time();
                    }
                }else if($this->isUpdateTime($columnName)){
                    $insertData[$columnName] = time();
                }else if(isset($data[$columnName])){
                    $insertData[$columnName] = empty($data[$columnName])?0:strtotime($data[$columnName]);
                }
            }elseif(isset($data[$columnName])){
                $insertData[$columnName] = (new ParamConvert(__NAMESPACE__.'\\params\\type\\'))->run($column['DATA_TYPE'],$data[$columnName]);
            }
        }

        return $insertData;
    }
}
