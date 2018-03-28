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
    const class_path = "App\Common\Tools\Jwt";
    public static function make($class_name = 'DefaultPayload') {
        $abstract_class = static::class_path . '\\' . $class_name;
        return  new $abstract_class;
    }
}