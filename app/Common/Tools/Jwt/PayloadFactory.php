<?php

namespace App\Common\Tools\Jwt;
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 23:45
 */

class PayloadFactory
{
    public static function make($class_name="DefaultPayload"){
        //$payload = new $class_name();
        return  new DefaultPayload();
    }

}