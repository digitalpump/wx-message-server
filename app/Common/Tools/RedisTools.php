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

    public static function getJwtTokenKey($did) {
        $prefix  = config('app.token_key_prefix','jwt_token_uid_');
        return $prefix.$did;
    }

    public static function getJwtRefreshTokenKey($did) {
        $prefix  = config('app.refresh_token_key_prefix','jwt_refresh_token_');
        return $prefix.$did;
    }

    public static function getWxAccessTokenKey() {
        $prefix  = config('app.weixin_access_token_key','wx_access_token_');
    }

    public static function getAppSecretKey($appid) {
        $prefix  = config('app.local_app_secret_key','local_app_secret_');
        return $prefix.$appid;
    }

    public static function setex($key,$ttl_minutes,$value) {

        return Redis::setex($key,$ttl_minutes*60,$value);
    }

    public static function get($key) {
        return Redis::get($key);
    }

    public static function setJwtToken($uid, $ttl_minutes, $value) {
        return static::setex(static::getJwtTokenKey($uid),$ttl_minutes,$value);
    }

    public static function getJwtToken($uid) {
        return Redis::get(static::getJwtTokenKey($uid));
    }

    public static function setJwtRefreshToken($did, $ttl_minutes, $value) {
        return static::setex(static::getJwtRefreshTokenKey($did),$ttl_minutes,$value);
    }

    public static function getJwtRefreshToken($did) {
        return static::get(static::getJwtRefreshTokenKey($did));
    }

    public static function setWxAccessToken($ttl_minutes,$value) {
        return static::setex(static::getWxAccessTokenKey(),$ttl_minutes,$value);
    }

    public static function getWxAccessToken() {
        return static::get(static::getWxAccessTokenKey());
    }

    public static function setAppSecret($appid,$value) {
        return Redis::set(static::getAppSecretKey($appid),$value);
    }

    public static function getAppSecret($appid) {
        return Redis::get(static::getAppSecretKey($appid));
    }

}