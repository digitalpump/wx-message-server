<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/12
 * Time: 07:42
 */

namespace App\Common\Tools;


use App\Models\OauthUsers;
use App\Models\Users;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;
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

    public static function wxMiniProgramLogin($responseAccessToken,$uid=0,$bind=false) {
        if(empty($responseAccessToken)) return null;
        if(empty($responseAccessToken->openid)) {
            return null;
        }

        try {
            $oauth_model = OauthUsers::where('openid',$responseAccessToken->openid)->firstOrFail();
            $users = $oauth_model->users;

        }catch (ModelNotFoundException $e) {
            $users = static::createNewMiniProgram($responseAccessToken);
        }

        if(empty($users)) return null;

        return ['uid'=>$users->id,'role'=>$users->user_type,'status'=>$users->user_status];
    }

    public static function createNewMiniProgram($miniProaramAccessToken) {
        try {
            $users = DB::transaction(function () use ($miniProaramAccessToken){
                $aouth_model = new OauthUsers();
                $aouth_model->openid = $miniProaramAccessToken->openid;
                if(!empty($miniProaramAccessToken->unionid)) $aouth_model->unionid = $miniProaramAccessToken->unionid;
                $aouth_model->session_key = $miniProaramAccessToken->session_key;

                $users = new Users();
                $users->nice_name = "游客_" . str_random(8);
                $users->save();
                $users->oauths()->save($aouth_model);
                return $users;
            });
            return $users;
        } catch (\Exception $e) {
            Log::err($e->getMessage());
            return null;
        }

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