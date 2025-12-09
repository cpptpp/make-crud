<?php

namespace Meioa\Tools\action;



use think\facade\Db;

class DeleteRow extends BaseAction
{
    public function run($data){
        if(!isset($data[$this->tablePk]) || $data[$this->tablePk]<1){
            return Result::array(112,'参数错误！');
        }
        $res = Db::table($this->table)->where([$this->tablePk=>$data[$this->tablePk]])->delete();
        if($res<1){
            return Result::array(110,'删除失败！',$res);
        }else{
            return Result::array(0,'',$res);
        }
    }
}
