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
        $obj = $request->json()->all();
        if(empty($obj)) return $this->error(HttpStatusCode::BAD_REQUEST,"Bad request.json body is empty.");
        $keys = $request->json()->keys();
        Log::debug(\GuzzleHttp\json_encode($keys));
        Log::debug(\GuzzleHttp\json_encode($obj));
        if(empty($obj['message'])) return $this->error(HttpStatusCode::BAD_REQUEST,"Bad request.message is empty.");

        $app_key = $request->header('appkey');
        //检查appid 是否存在并属于该用户 with app_key, ---》好像不需要，微信用户的openid 是分开的，不会被串发

        $wx_appid = "";
        if(empty($obj['appid'])) { //方便用户和安全，不输入wx appid的情况下，通过appkey获得wx appid，但仅限于该账号下只有一个微信appid 的情况，有多个的话必须指定
            $wx_appid = $this->getWxAppIdByAppKey($app_key);
            if(empty($wx_appid)) {
                return $this->error(HttpStatusCode::FORBIDDEN,"No weixin appid exist for key=".$app_key);
            }
        } else {
            if(!$this->paramsChecking($obj['appid'])) {
                return $this->error(HttpStatusCode::BAD_REQUEST,"Appid look bad.");
            }
            $wx_appid = $obj['appid'];
        }

        $key = env('MESSAGE_POOL_KEY_RPEFIX') . $wx_appid;
        try {
            $result =  Redis::rpush($key,json_encode($obj['message']));
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
        $appid = $request->get('appid');
        if(empty($appid) || !$this->paramsChecking($appid)) {
            return $this->error(HttpStatusCode::BAD_REQUEST,"Appid look bad.");
        }
        $accessToken = RedisTools::getWxAccessToken($appid);
        if (empty($accessToken)) {
            return $this->error(HttpStatusCode::NO_CONTENT,"None access token.");
        }
        return $this->success($accessToken);
    }

    private function paramsChecking($appid,$min=5,$max=32,$alnum=true) {
        $len = strlen($appid);
        if($len<$min ||$len>$max) return false;
        if($alnum) return ctype_alnum($appid);
        return true;
    }

    /**
     * TODO 解封用户接口
     * @param Request $request
     */

    public function deblockingUser(Request $request) {

    }
}