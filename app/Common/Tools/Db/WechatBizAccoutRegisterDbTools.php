<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/30
 * Time: 19:59
 */

namespace App\Common\Tools\Db;

use App\Models\BizOrder;
use App\Models\OauthUser;
use App\Models\User;
use App\Models\VendorAppSecret;
use App\Models\VendorWxAccount;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;
class WechatBizAccoutRegisterDbTools
{


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
        return User::find($uid)->bizorders()->where("status",1)->first();
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

    public static function updateBizOrder(&$bizOrder,$step,$value,$deployServer="ps-001") {
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

                return DB::transaction(function () use (&$bizOrder,$deployServer){

                    $wxAccount = new VendorWxAccount();
                    $wxAccount->vendor_id = $bizOrder->user_id;
                    $wxAccount->account_name = "offical_account";
                    $wxAccount->wx_appid = $bizOrder->app_id;
                    $wxAccount->wx_secret = $bizOrder->app_secret;
                    $wxAccount->deploy_server = $deployServer;
                    $wxAccount->status = -1;
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

    public static function getWxAccount($uid) {
        try {
            return VendorWxAccount::where('vendor_id',$uid)
                ->where('account_name','offical_account')
                ->firstOrFail();
        }catch (ModelNotFoundException $e) {
            return null;
        }
    }
}