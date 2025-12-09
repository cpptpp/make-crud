<?php

namespace Meioa\Tools\action;


class BaseAction
{
    public  $table;
    public  $tablePk;
    public function __construct($table,$tablePk =null){

        $this->table = $table;
        $this->tablePk = $tablePk;
    }
}
