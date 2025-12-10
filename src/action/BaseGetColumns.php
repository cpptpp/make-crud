<?php

namespace Meioa\Tools\action;



use Meioa\Tools\TableCache;


class BaseGetColumns extends BaseAction
{
    public function run(): array
    {
        $res = (new TableCache())->getColumns($this->table);
        return Result::array(0,'',$res);
    }


}
