<?php

namespace Meioa\Tools\action;

class ParamConvert
{
    private $_ns;
    public function __construct($ns)
    {
        $this->_ns = $ns;
    }

    public function run($type,$value){



        $typeConvert = $this->_ns.ucfirst($type).'Convert';
        //var_dump($typeConvert);die;
        if(class_exists($typeConvert)){
            $filterClassObj = new $typeConvert();
            return call_user_func(array($filterClassObj,'run'), $value);
        }else{
            return $value;
        }
    }
}
