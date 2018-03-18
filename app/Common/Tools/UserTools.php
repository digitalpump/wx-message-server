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

    public static function wxMiniProgramLogin($responseAccessToken,$ip = "",$uid=0,$bind=false) {
        if(empty($responseAccessToken)) return null;
        if(empty($responseAccessToken->openid)) {
            return null;
        }

        try {
            $oauth_model = OauthUsers::where('openid',$responseAccessToken->openid)
                ->where('from',OauthUsers::FROM_WX_MINI_PROGRAM)
                ->firstOrFail();
            $users = $oauth_model->users;
            $oauth_model->last_login_ip = $ip;
            $oauth_model->login_times +=1;
            $oauth_model->session_key = $responseAccessToken->session_key;
            $oauth_model->save();

        }catch (ModelNotFoundException $e) {
            $users = static::createNewMiniProgram($responseAccessToken,$ip);
        }

        if(empty($users)) return null;

        return ['uid'=>$users->id,'role'=>$users->user_type,'status'=>$users->user_status];
    }

    public static function createNewMiniProgram($miniProaramAccessToken,$ip="") {
        try {
            $users = DB::transaction(function () use ($miniProaramAccessToken,$ip){
                $oauth = new OauthUsers();
                $oauth->openid = $miniProaramAccessToken->openid;
                if(!empty($miniProaramAccessToken->unionid)) $oauth->unionid = $miniProaramAccessToken->unionid;
                $oauth->session_key = $miniProaramAccessToken->session_key;
                $oauth->last_login_ip = $ip;
                $oauth->from = OauthUsers::FROM_WX_MINI_PROGRAM;
                $users = new Users();
                $users->nice_name = "游客_" . str_random(8);
                $users->save();
                $users->oauths()->save($oauth);
                return $users;
            });
            return $users;
        } catch (\Exception $e) {
            Log::err($e->getMessage());
            return null;
        }

    }

    public static function getWxMiniProgramSessionKeyById($uid) {
        $oauth = Users::find($uid)->oauths()->where('from',OauthUsers::FROM_WX_MINI_PROGRAM)->first();
        if(empty($oauth)) return null;
        return $oauth->session_key;
    }

    public static function updateUserWithInfoFromWx($uid,$data) {
        $user = Users::find($uid);
        if(empty($user)) return;
        $user->avater = $data->avatarUrl;
        $user->third_party_name = $data->nickName;
        return $user->save();
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