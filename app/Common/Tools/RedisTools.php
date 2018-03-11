<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 11:15
 */

namespace App\Common\Tools;

use Redis;
class RedisTools
{
    public static function setex($key,$ttl_minutes,$value) {
        return Redis::setex($key,$ttl_minutes*60,$value);
    }

    public static function get($key) {
        return Redis::get($key);
    }

}