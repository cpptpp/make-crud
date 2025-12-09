<?php

namespace Meioa\Tools\action\params\type;

class SetConvert
{
    /**
     * @param $value
     * @return string
     */
    public  function run($value){
        return empty($value)?'':implode(',',$value);
    }
}
