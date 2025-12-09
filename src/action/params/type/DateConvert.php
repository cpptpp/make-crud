<?php

namespace Meioa\Tools\action\params\type;

class DateConvert
{

    public function run($value){
        return empty($value)?'':date('Y-m-d',strtotime($value));

    }
}
