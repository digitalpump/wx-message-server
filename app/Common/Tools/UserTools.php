<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/12
 * Time: 07:42
 */

namespace App\Common\Tools;


class UserTools
{
    /**
     * @param $wxAccessTokenObject
     * @return array int
     */
    public static function wxLogin($wxAccessToken) {
        //TODO
        //$wxAccessToken->access_token;
        //$wxAccessToken->expires_in;
        //$wxAccessToken->refresh_token;
        //$wxAccessToken->openid;
        //$wxAccessToken->scope;
        //$wxAccessToken->unionid;

        return ['uid'=>2,'role'=>4,'status'=>0];
    }

    public static function wxMiniProgramLogin($responseAccessToken) {
        //$responseAccessToken->openid
        //$responseAccessToken->session_key
        //$$responseAccessToken->unionid

        return ['uid'=>8,'role'=>4,'status'=>0];
    }

    /**
     * @param $email
     * @param $password
     * @return array int
     */
    public static function emailLogin($email,$password) {
        //TODO
        return ['uid'=>2,'role'=>4,'status'=>0];
    }

    public static function getWxRefreshToken($wxOpenid) {
        //TODO
        return "wx_refresh_token";
    }

    public static function updateWxTokens($wxOpenid,$newAccessToken,$newRefreshToken) {

    }

    public static function saveWxUserInfo($uid,$userinfo) {

    }
}