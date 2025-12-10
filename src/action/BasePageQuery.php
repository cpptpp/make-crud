<?php

namespace Meioa\Tools\action;



use think\facade\Db;

class BasePageQuery extends BaseAction
{
    private $_withoutField;
    public function setWithoutField($field){
        $this->_withoutField = $field;
        return $this;
    }

    private function _getColumns(){
        $columnRes = (new BaseGetColumns($this->table,$this->tablePk))->run();
        return $columnRes['data'];
    }

    private function _isTimeColumn($columnName){
        return preg_match("/(time)$/", $columnName);
    }
    private function _createCondition($columns,$param){

        $where =[];
        foreach ($columns as $column){

            if(isset($param[$column['COLUMN_NAME']]) && !empty($param[$column['COLUMN_NAME']])){
                if($column['DATA_TYPE'] == 'date'){
                    $where[] = [$column['COLUMN_NAME'],'between',[date('Y-m-d',strtotime($param[$column['COLUMN_NAME']][0])),date('Y-m-d',strtotime($param[$column['COLUMN_NAME']][1]))]];
                }elseif($this->_isTimeColumn($column['COLUMN_NAME'])){
                    $where[] = [$column['COLUMN_NAME'],'between',[strtotime($param[$column['COLUMN_NAME']][0]),strtotime($param[$column['COLUMN_NAME']][1])]];
                }else{
                    $where[] = [$column['COLUMN_NAME'],'=',$param[$column['COLUMN_NAME']]];
                }
            }

        }
        return $where;
    }

    private function _getRes($columns,$data){
        foreach ($columns as $column){
            $dataType = $column['DATA_TYPE'];
            if( in_array($dataType,['set','date']) ){
                //var_dump($column,$data);die;
                foreach ($data as $key=> $item){
                    if(isset($item[$column['COLUMN_NAME']]) && !empty($item[$column['COLUMN_NAME']])){
                        if($dataType== 'set'){
                            $data[$key][$column['COLUMN_NAME']] = explode(',',$item[$column['COLUMN_NAME']]);
                        }elseif($dataType== 'date'){
                            $data[$key][$column['COLUMN_NAME']] = $item[$column['COLUMN_NAME']]!='0000-00-00'?$item[$column['COLUMN_NAME']]:'';
                        }

                    }
                }
            }
        }

        return $data;
    }
    public function run($param){

        $page = $param['page']??1;
        $num = $param['num']??10;
        $columns = $this->_getColumns();
        $where = $this->_createCondition($columns,$param);
        //var_dump($where);die;
        $startNum = ($page-1)*$num;
        $db = Db::table($this->table)->where($where);
        if(!empty($this->_withoutField)){
            $db = $db->withoutField($this->_withoutField);
        }
        $lists= $db->limit($startNum,$num)->order($this->tablePk,'desc')->select()->toArray();
        $data['list'] = $this->_getRes($columns,$lists);
        //$data['list'] = Db::table($this->table)->limit($startNum,$num)->order($this->tablePk,'desc')->select();

        $data['total'] = $db->count();

        return Result::array(0,'',$data);
    }
}
