<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/25
 * Time: 11:27
 */

namespace App\Common\Tools;
use App\Common\Tools\Jwt\JwtAuth;
use App\Common\Tools\Jwt\PayloadFactory;
use Carbon\Carbon;

class JwtTokenTools
{

    /**
     * 生成新的token 接口
     * @param JwtAuth $jwtAuth
     * @param $uid
     * @return string
     */
    private static function generateNewToken(JwtAuth $jwtAuth, $uid)
    {
        $token_ttl = app('JwtConfig')->getTokenTtl();
        $token =  $jwtAuth->encode(function () use ($uid,$token_ttl) {
            return PayloadFactory::make()->setTTL($token_ttl)->buildClaims(['sub' => $uid])->getClaims();
        },false);
        if(empty($token)) return "";

        RedisTools::setJwtToken($uid,$token_ttl,$token);
        return $token;
    }

    /**
     * 生成新的刷新token接口
     * @param JwtAuth $jwtAuth
     * @param $uid
     * @return string
     */
    private static function generateNewRefreshToken(JwtAuth $jwtAuth, $uid)
    {
        $refresh_ttl = app('JwtConfig')->getRefreshTokenTtl();
        $refresh_delay = app('JwtConfig')->getRefreshTokenDelay();
        $token = $jwtAuth->encode(function () use ($uid, $refresh_ttl,$refresh_delay) {
            $nbf = Carbon::now()->addMinutes($refresh_delay)->timestamp;
            return PayloadFactory::make()->setTTL($refresh_ttl)->buildClaims(['nbf' => $nbf, 'sub' => $uid])->getClaims();
        },true);

        if(empty($token)) return $token;
        RedisTools::setJwtRefreshToken($uid,$refresh_ttl,$token);
        return $token;
    }

    /**
     * 生成新的微信刷新token接口
     * @param JwtAuth $jwtAuth
     * @param $uid
     * @param $openid
     * @return string
     */
    private static function generateNewWxRefreshToken(JwtAuth $jwtAuth, $uid, $openid)
    {
        $refresh_ttl = app('JwtConfig')->getRefreshTokenTtl();
        $refresh_delay = app('JwtConfig')->getRefreshTokenDelay();

        $token = $jwtAuth->encode(function () use ($uid, $openid, $refresh_ttl,$refresh_delay) {
            $nbf = Carbon::now()->addMinutes($refresh_delay)->timestamp;
            return PayloadFactory::make()->setTTL($refresh_ttl)->buildClaims(['openid' => $openid, 'nbf' => $nbf, 'sub' => $uid])->getClaims();
        },true);
        if(empty($token)) return $token;
        RedisTools::setJwtRefreshToken($uid,$refresh_ttl,$token);
        return $token;
    }

    /**
     * 返回普通登录token
     * @param $uid
     * @param $did
     * @return array
     */
    public static function newNormallyToken($uid)
    {
        $jwtAuth = new JwtAuth();
        $jwtAuth->setJwtConfigure(app('JwtConfig'));
        $token = static::generateNewToken($jwtAuth, $uid);
        $refresh_token = static::generateNewRefreshToken($jwtAuth, $uid);
        return ['token' => app('JwtConfig')->getAuthMethod() . $token
            , 'refresh_token' => app('JwtConfig')->getAuthMethod() . $refresh_token];
    }


    /**
     * 返回微信登录 token
     * @param $uid
     * @param $did
     * @param $openid
     * @return array
     */
    public static function newWxLoginToken($uid, $openid)
    {
        $jwtAuth = new JwtAuth();
        $jwtAuth->setJwtConfigure(app('JwtConfig'));
        $token = static::generateNewToken($jwtAuth, $uid);
        $refresh_token = static::generateNewWxRefreshToken($jwtAuth, $uid, $openid);
        return ['token' => app('JwtConfig')->getAuthMethod() . $token
            , 'refresh_token' => app('JwtConfig')->getAuthMethod() . $refresh_token];

    }
}