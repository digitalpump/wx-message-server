<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/12
 * Time: 07:42
 */

namespace App\Common\Tools;


use App\Models\BizOrder;
use App\Models\OauthUser;
use App\Models\User;
use App\Models\VendorAppSecret;
use App\Models\VendorWxAccount;
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

    /**
     * 小程序用户数据库注册登录接口
     * @param $responseAccessToken  微信小程序code换取 session 返回数据
     * @param string $ip
     * @param int $uid
     * @param bool $bind            是否绑定原有用户
     * @return array|null
     */
    public static function wxMiniProgramLogin($responseAccessToken,$ip = "",$uid=0,$bind=false) {
        if(empty($responseAccessToken)) return null;
        if(empty($responseAccessToken->openid)) {
            return null;
        }

        try {
            $oauth_model = OauthUser::where('openid',$responseAccessToken->openid)
                ->where('from',OauthUser::FROM_WX_MINI_PROGRAM)
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
                $oauth = new OauthUser();
                $oauth->openid = $miniProaramAccessToken->openid;
                if(!empty($miniProaramAccessToken->unionid)) $oauth->unionid = $miniProaramAccessToken->unionid;
                $oauth->session_key = $miniProaramAccessToken->session_key;
                $oauth->last_login_ip = $ip;
                $oauth->from = OauthUser::FROM_WX_MINI_PROGRAM;
                $users = new User();
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

    public static function weChatUserRegisterAndLogin($appid,$openid,$unionid="") {
        try {
            $oauth = OauthUser::where('openid',$openid)
                ->where('fromwhere',$appid)
                ->firstOrFail();
            return $oauth->user;

        }catch (ModelNotFoundException $e) {
            return static::createNewWeChatUser($appid,$openid,$unionid);
        }

    }

    public static function createNewWeChatUser($appid,$openid,$unionid="") {
        try {
            return DB::transaction(function () use ($appid,$openid,$unionid){
                $oauth = new OauthUser();
                $oauth->openid = $openid;
                $oauth->unionid = $unionid;
                $oauth->fromwhere = $appid;
                $user = new User();
                $user->nickname = "爽客_" . str_random(8);
                $user->role = User::USER_TYPE_NORMAL;
                $user->status = User::USER_STATUS_NORMAL;
                $user->save();
                $user->oauths()->save($oauth);
                return $user;
            });

        } catch (\Exception $e) {
            Log::err($e->getMessage());
            return null;
        }
    }

    public static function getBizOrder($uid) {
        //后期放到redis
        return User::find($uid)->bizorders()->first();
    }

    public static function createNewBizOrder($uid) {
        //后期直接在redis中他建
        try {
            $bizOrder = new BizOrder();
            $bizOrder->user_id = $uid;
            $bizOrder->process_status = 0;
            $bizOrder->update_code = mt_rand(1000,10000);
            if(!$bizOrder->save()) return null;
            return $bizOrder;
        } catch (\Exception $exception) {
            return null;
        }
    }

    public static function updateBizOrder(&$bizOrder,$step,$value) {
        $oldProcessStatus = $bizOrder->process_status;

        if($step ==1) {
            $bizOrder->process_status = $oldProcessStatus+2;
            $bizOrder->app_id = $value;
        } else {
            $bizOrder->process_status = $oldProcessStatus+4;
            $bizOrder->app_secret = $value;
        }
        try {
            if($bizOrder->process_status == 6) {
                //TODO 完成
                $appid = $oldProcessStatus==2?$bizOrder->app_id:$value;
                $secret = $oldProcessStatus==4?$bizOrder->app_secret:$value;
                return DB::transaction(function () use (&$bizOrder){

                    $wxAccount = new VendorWxAccount();
                    $wxAccount->vendor_id = $bizOrder->user_id;
                    $wxAccount->account_name = "offical_account";
                    $wxAccount->wx_appid = $bizOrder->app_id;
                    $wxAccount->wx_secret = $bizOrder->app_secret;
                    $wxAccount->deploy_server = "ps-001";
                    $wxAccount->status = 0;
                    $wxAccount->save();
                    return $bizOrder->save();
                });
            } else {
                return $bizOrder->save();
            }
        } catch (\Exception $exception) {
            Log::err($exception->getMessage());
            return false;
        }

    }

    public static function getWxMiniProgramSessionKeyById($uid) {
        $oauth = User::find($uid)->oauths()->where('from',OauthUser::FROM_WX_MINI_PROGRAM)->first();
        if(empty($oauth)) return null;
        return $oauth->session_key;
    }

    public static function updateUserWithInfoFromWx($uid,$data) {
        $user = User::find($uid);
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