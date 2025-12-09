<?php

namespace Meioa\Tools\action;



use Meioa\Tools\TableCache;


class GetColumns extends BaseAction
{
    public function run(){
        $res = (new TableCache())->getColumns($this->table);
        return Result::array(0,'',$res);
    }


}
