<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/10
 * Time: 06:13
 */

namespace App\Common\Tools;


use GuzzleHttp\Client;

class WxTokenTools
{

    const WX_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
    const WX_REFRESH_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/refresh_token";
    const WX_USERINFO_URL = "https://api.weixin.qq.com/sns/userinfo";
    const WX_AUTH_URL = "https://api.weixin.qq.com/sns/auth";
    /*
     *
     *
     * {
            "access_token":"ACCESS_TOKEN",
            "expires_in":7200,
            "refresh_token":"REFRESH_TOKEN",
            "openid":"OPENID",
            "scope":"SCOPE"
        }

        {
            "errcode":40029,"errmsg":"invalid code"
        }
     */
    public static function getAccessToken($appid,$secret,$code)
    {
        $httpClient = new Client();
        try {

            $response = $httpClient->request('GET', WxTokenTools::WX_ACCESS_TOKEN_URL
                , ['query' => ['appid' => $appid, 'secret' => $secret, 'code' => $code, 'grant_type' => 'authorization_code']]);
            if ($response->getStatusCode() != 200) {
                return null;
            }
            return \GuzzleHttp\json_decode($response->getBody());
        } catch (\Exception $exception) {
            return null;
        }
    }


    /*
        {
        "access_token":"ACCESS_TOKEN",
        "expires_in":7200,
        "refresh_token":"REFRESH_TOKEN",
        "openid":"OPENID",
        "scope":"SCOPE"
        }

        {
            "errcode":40030,"errmsg":"invalid refresh_token"
        }
    */
    public static function refreshAccessToken($appid,$refreshToken) {

        $httpClient = new Client();
        try {
            $response = $httpClient->request('GET',WxTokenTools::WX_REFRESH_TOKEN_URL
                ,['query'=>['appid'=>$appid,'refresh_token'=>$refreshToken,'grant_type'=>'refresh_token']]);
            if ($response->getStatusCode() != 200 ) {
                return null;
            }
            return \GuzzleHttp\json_decode($response->getBody());
        } catch (\Exception $exception) {

            return null;
        }

    }


    /*
     * 获取用户信息
     *
        {
        "openid":"OPENID",
        "nickname":"NICKNAME",
        "sex":1,
        "province":"PROVINCE",
        "city":"CITY",
        "country":"COUNTRY",
        "headimgurl": "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
        "privilege":[
        "PRIVILEGE1",
        "PRIVILEGE2"
        ],
        "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
        }

        {
            "errcode":40003,"errmsg":"invalid openid"
        }
    */

    public static function userinfo($openid,$accessToken)
    {
        $httpClient = new Client();
        try {

            $response = $httpClient->request('GET', WxTokenTools::WX_USERINFO_URL
                , ['query' => ['openid' => $openid, 'access_token' => $accessToken]]);
            if ($response->getStatusCode() != 200) {
                return null;
            }
            return \GuzzleHttp\json_decode($response->getBody());
        }catch (\Exception $exception) {
            return null;
        }
    }
    /*
     * 验证access_token
     *
        正确的Json返回结果：
        {
        "errcode":0,"errmsg":"ok"
        }
        错误的Json返回示例:
         {
                "errcode":40003,"errmsg":"invalid openid"
        }
    */
    public static function auth($openid,$accessToken) {
        $httpClient = new Client();
        try {
            $response = $httpClient->request('GET',WxTokenTools::WX_AUTH_URL
                ,['query'=>['openid'=>$openid,'access_token'=>$accessToken]]);
            if ($response->getStatusCode() != 200 ) {
                return null;
            }
            return $response->getBody();
        } catch (\Exception $exception) {
            return null;
        }

    }
}