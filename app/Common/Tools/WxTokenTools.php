<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/10
 * Time: 06:13
 */

namespace App\Common\Tools;


use App\Common\Tools\Configure\WeixinConfigure;
use GuzzleHttp\Client;
use Log;
class WxTokenTools
{

    const WX_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
    const WX_REFRESH_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/refresh_token";
    const WX_USERINFO_URL = "https://api.weixin.qq.com/sns/userinfo";
    const WX_AUTH_URL = "https://api.weixin.qq.com/sns/auth";
    const WX_JSCODE2SESSION_URL = "https://api.weixin.qq.com/sns/jscode2session";

    const WX_TOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token";
    const WX_TEMPLATE_MESSAGE_URL = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=";

    /*
   //正常返回的JSON数据包
       {
       "openid": "OPENID",
       "session_key": "SESSIONKEY",
       }

       //满足UnionID返回条件时，返回的JSON数据包
           {
               "openid": "OPENID",
           "session_key": "SESSIONKEY",
           "unionid": "UNIONID"
       }
       //错误时返回JSON数据包(示例为Code无效)
           {
               "errcode": 40029,
           "errmsg": "invalid code"
       }
   */

    /**
     * 微信小程序通过jscode 换取 session 接口
     * @param WeixinConfigure $wConfigure
     * @param $code
     * @return mixed|null
     */
    public static function jscode2Session(WeixinConfigure $wConfigure,$code) {
        if(empty($wConfigure)) return null;
        $secret = $wConfigure->getMiniProgramSecret();
        $appid = $wConfigure->getMiniProgramAppId();
        if(empty($secret) || empty($appid)) {
            Log::err("Weixin configure for mini program not found.");
            return null;
        }
        $httpClient = new Client();
        try {

            $response = $httpClient->request('GET', WxTokenTools::WX_JSCODE2SESSION_URL
                , ['query' => ['appid' => $appid, 'secret' => $secret, 'js_code' => $code, 'grant_type' => 'authorization_code']]);

            if ($response->getStatusCode() != 200) {
                return null;
            }
            return \GuzzleHttp\json_decode($response->getBody());
        } catch (\Exception $exception) {
            return null;
        }
    }
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


    public static function getAccessToken(WeixinConfigure $weixinConfigure,$code)
    {
        $appid = $weixinConfigure->getOfficialAccountAppId();
        $secret = $weixinConfigure->getOfficialAccountSecret();
        if(empty($appid) || empty($secret)) {
            Log::err("Weixin configure for official account  not found.");
            return null;
        }
        $httpClient = new Client();
        try {
            $response = $httpClient->request('GET', WxTokenTools::WX_ACCESS_TOKEN_URL
                , ['query' => ['appid' => $appid, 'secret' => $secret, 'code' => $code, 'grant_type' => 'authorization_code']]);

            if ($response->getStatusCode() != 200) {
                return null;
            }
            Log::debug("@getAccessToken:".$response->getBody());
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
            Log::debug("@refreshAccessToken:".$response->getBody());
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

    /**
     *
     * 获取微信token
     * @param $appid
     * @param $secret
     * @return null|\Psr\Http\Message\StreamInterface
     * {"access_token": "ACCESS_TOKEN", "expires_in": 7200}
     * {"errcode": 40013, "errmsg": "invalid appid"}
     */
    public static function getToken($appid,$secret) {
        $httpClient = new Client();
        try {
            $response = $httpClient->request('GET',WxTokenTools::WX_TOKEN_URL
                ,['query'=>['appid'=>$appid,'secret'=>$secret,'grant_type'=>'client_credential']]);
            if ($response->getStatusCode() != 200 ) {
                return null;
            }
            return $response->getBody();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * 发送微信模板消息接口
     *
     * @param $accessToken
     * @param $message
     * @return null|\Psr\Http\Message\StreamInterface
     * {
        "errcode": 0,
        "errmsg": "ok"
        }
     */
    public static function sendTemplateMessage($accessToken,$message) {
        $url = WxTokenTools::WX_TEMPLATE_MESSAGE_URL . $accessToken;
        $httpClient = new Client();
        try {
            $response = $httpClient->post($url,['json'=>$message]);
            if ($response->getStatusCode() != 200 ) {
                return null;
            }
            return $response->getBody();
        } catch (\Exception $exception) {
            return null;
        }
    }

}