<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 11:15
 */

namespace App\Common\Tools;

use Illuminate\Support\Facades\Redis;
use Log;
class RedisTools
{

    public static function getTokenKey($did) {
        $prefix  = config('app.token_key_prefix','jwt_token_uid_');
        return $prefix.$did;
    }

    public static function getWxRefreshTokenKey($did) {
        $prefix  = config('app.wx_refresh_token_key_prefix','wx_refresh_token_');
        return $prefix.$did;
    }

    public static function getWxSessionKeyName($uid) {
        $prefix = config('app.wx_session_key_prefix','wx_session_key_');
        return $prefix.$uid;
    }

    public static function setWxSesssionKey($uid,$value){
        return Redis::set(static::getWxSessionKeyName($uid),$value);
    }

    public static function getWxSessionKey($uid) {
        return Redis::get(static::getWxSessionKeyName($uid));
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

    public static function setWxRefreshToken($did,$ttl_minutes,$value) {
        return static::setex(static::getWxRefreshTokenKey($did),$ttl_minutes,$value);
    }

    public static function getWxRefreshToken($did) {
        return Redis::get(static::getWxRefreshTokenKey($did));
    }

}