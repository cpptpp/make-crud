<?php

namespace Meioa\Tools\action;

use think\facade\Db;

class BaseUpdateRow extends BaseAction
{
    public function run($data){

        if(!isset($data[$this->tablePk]) || $data[$this->tablePk]<1){
            return Result::array(112,'参数错误！');
        }
        $columns = (new BaseGetColumns($this->table,$this->tablePk))->run();
        $data = (new ParamAnalyse())->validData($data,$columns,true);
        $res = Db::table($this->table)->where([$this->tablePk=>$data[$this->tablePk]])->update($data);
        if($res<1){
            return Result::array(113,'更新失败！',$res);
        }else{
            return Result::array(0,'',$res);
        }
    }
}