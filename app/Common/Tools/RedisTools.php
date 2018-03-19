<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 11:15
 */

namespace App\Common\Tools;

use Illuminate\Support\Facades\Redis;
class RedisTools
{

    public static function getTokenKey($did) {
        $prefix  = config('app.token_key_prefix','jwt_token_uid_');
        return $prefix.$did;
    }

    public static function getRefreshTokenKey($did) {
        $prefix  = config('app.refresh_token_key_prefix','jwt_refresh_token_');
        return $prefix.$did;
    }



    public static function setex($key,$ttl_minutes,$value) {

        return Redis::setex($key,$ttl_minutes*60,$value);
    }

    public static function get($key) {
        return Redis::get($key);
    }

    public static function setToken($uid,$ttl_minutes,$value) {
        return static::setex(static::getTokenKey($uid),$ttl_minutes,$value);
    }

    public static function getToken($uid) {
        return Redis::get(static::getTokenKey($uid));
    }

    public static function setRefreshToken($did,$ttl_minutes,$value) {
        return static::setex(static::getRefreshTokenKey($did),$ttl_minutes,$value);
    }

    public static function getRefreshToken($did) {
        return static::get(static::getRefreshTokenKey($did));
    }

}