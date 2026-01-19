<?php

namespace Meioa\Tools\action;



use think\facade\Db;

class BaseAddRow extends BaseAction
{

    public function run($data): array
    {

        $columns = (new BaseGetColumns($this->table,$this->tablePk))->run();
        $data = (new ParamAnalyse())->validData($data,$columns,false);
        $res = Db::table($this->table)->insertGetId($data);

        if($res<1){
            return Result::array(110,'添加失败！',$res);
        }else{
            return Result::array(0,'',$res);
        }
    }

}
