<?php

namespace Meioa\Tools\action\params\type;

class DateConvert
{

    public function run($value){
        return empty($value)?null:date('Y-m-d',strtotime($value));

    }
}
