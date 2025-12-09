<?php

namespace Meioa\Tools\action;


class Result
{

    public static function array($error = 110, $msg = '', $res = null ){
        $data['error'] = $error;
        $data['message']   = $msg;
        $data['data']  = $res;
        return $data;
    }
}
