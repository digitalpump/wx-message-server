<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/25
 * Time: 13:22
 */

namespace App\Http\Api\Controllers;

use App\Common\Tools\HttpStatusCode;
use App\Common\Tools\RedisTools;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Log;
use DB;


class MessageApiController extends Controller
{

    /**
     *
     * 发送消息接口
     * @param Request $request
     */
    public function sendMessage(Request $request) {
        $jsonbody = $request->json()->all();
        if(empty($jsonbody)) return $this->error(HttpStatusCode::BAD_REQUEST,"Bad request.json body is empty.");

        if(!empty($jsonbody[0])) {
            $obj = json_decode($jsonbody[0]);
            $message = $obj->message;
            $wx_appid = $obj->appid;
        } else {
            $message = $jsonbody['message'];
            $wx_appid = $jsonbody['appid'];
        }


        if(empty($message)) return $this->error(HttpStatusCode::BAD_REQUEST,"Bad request.message is empty.");

        $app_key = $request->header('appkey');
        //检查appid 是否存在并属于该用户 with app_key, ---》好像不需要，微信用户的openid 是分开的，不会被串发


        if(empty($wx_appid)) { //方便用户和安全，不输入wx appid的情况下，通过appkey获得wx appid，但仅限于该账号下只有一个微信appid 的情况，有多个的话必须指定
            $wx_appid = $this->getWxAppIdByAppKey($app_key);
            if(empty($wx_appid)) {
                return $this->error(HttpStatusCode::FORBIDDEN,"No weixin appid exist for key=".$app_key);
            }
        }

        if(!$this->paramsChecking($wx_appid)) {
            return $this->error(HttpStatusCode::BAD_REQUEST,"Appid look bad.");
        }


        $key = env('MESSAGE_POOL_KEY_RPEFIX') . $wx_appid;

        try {
            $result =  Redis::rpush($key,json_encode($message));
        } catch (\Exception $exception) {
            Log::error("Push message into redis failed." . $exception->getMessage());
            return $this->error(HttpStatusCode::NOT_MODIFIED,"Send message failed.");
        }

        if (empty($result)) return $this->error(HttpStatusCode::NOT_MODIFIED,"Send message failed.");
        return $this->success($result);
    }

    private function getWxAppIdByAppKey($appkey) {
        $redis_key = "wx_appid_cached_by_" . $appkey;
        try {
            $result = Redis::get($redis_key);
            if(!empty($result)) return $result;
        } catch (\Exception $exception) {

        }

        try {
            $appid = DB::table("vendor_app_secret")
                ->join('vendor_wx_account',function ($join) {
                    $join->on('vendor_app_secret.vendor_id', '=', 'vendor_wx_account.vendor_id');
                })
                ->where('app_key',$appkey)
                ->where('is_default',1)
                ->value("wx_appid");
            if(empty($appid)) return "";
            Redis::setex($redis_key,3600,$appid);
            return $appid;

        } catch (\Exception $exception) {
            return "";
        }

    }



    /**
     * 获取 微信 accessToken 接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccessToken(Request $request) {
        $wx_appid = $request->get('appid');

        $app_key = $request->header('appkey');
        //检查appid 是否存在并属于该用户 with app_key, ---》好像不需要，微信用户的openid 是分开的，不会被串发


        if(empty($wx_appid)) { //方便用户和安全，不输入wx appid的情况下，通过appkey获得wx appid，但仅限于该账号下只有一个微信appid 的情况，有多个的话必须指定
            $wx_appid = $this->getWxAppIdByAppKey($app_key);
            if(empty($wx_appid)) {
                return $this->error(HttpStatusCode::FORBIDDEN,"No weixin appid exist for key=".$app_key);
            }
        }
        if(empty($wx_appid) || !$this->paramsChecking($wx_appid)) {
            return $this->error(HttpStatusCode::BAD_REQUEST,"appid looks bad.");
        }
        $accessToken = RedisTools::getWxAccessToken($wx_appid);
        if (empty($accessToken)) {
            return $this->error(HttpStatusCode::NO_CONTENT,"Access token not found.");
        }
        return $this->success($accessToken);
    }

    private function paramsChecking($appid,$min=5,$max=32,$alnum=true) {
        $len = strlen($appid);
        if($len<$min ||$len>$max) return false;
        if($alnum) return ctype_alnum($appid);
        return true;
    }


    public function wxServe(Request $request) {
        Log::debug(json_encode($request));
        Log::debug("GET ALL:".json_encode($_GET));
        Log::debug(json_encode($request->all()));

        $echostr = $request->get('echostr');
        Log::debug("echo str=".$echostr);

        return response($echostr);
    }

    /**
     * TODO 解封用户接口
     * @param Request $request
     */

    public function deblockingUser(Request $request) {

    }
}