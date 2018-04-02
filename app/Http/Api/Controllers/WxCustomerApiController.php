<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/4/2
 * Time: 05:11
 */

namespace App\Http\Api\Controllers;


use App\Common\Tools\CommonTools;
use App\Common\Tools\HttpStatusCode;
use App\Common\Tools\RedisTools;
use App\Common\Tools\WeChatAccessTools;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use DB;
class WxCustomerApiController extends Controller
{
    const ACTION_ADD = "ADD";
    const ACTION_UPDATE = "UPDATE";
    const ACTION_DEL = "DEL";

    public function addCustomer(Request $request) {
        return $this->commonReqest($request,self::ACTION_ADD);
    }

    public function updateCustomer(Request $request) {
        return $this->commonReqest($request,self::ACTION_UPDATE);
    }

    public function delCustomer(Request $request) {
        return $this->commonReqest($request,self::ACTION_DEL);
    }

    private function commonReqest(Request $request,$action) {
        $jsonbody = $request->json()->all();
        if(empty($jsonbody)) return $this->error(HttpStatusCode::BAD_REQUEST,"Bad request.json body is empty.");

        /**
         * 处理有可能的消息体多一层的情况
         */
        if(!empty($jsonbody[0])) {
            $message = $jsonbody[0];
        } else {
            $message = $jsonbody;
        }
        if(empty($message)) return $this->error(HttpStatusCode::BAD_REQUEST,"Bad request.message is empty.");
        $app_key = $request->header('appkey');
        $wx_appid = $this->getWxAppIdByAppKey($app_key);
        if(empty($wx_appid)) {
            return $this->error(HttpStatusCode::FORBIDDEN,"wexin appid not found for key=".$app_key);
        }

        if(!CommonTools::alnumCheck($wx_appid)) {
            return $this->error(HttpStatusCode::BAD_REQUEST,"appid looks bad.");
        }
        $accessToken = RedisTools::getWxAccessToken($wx_appid);
        if (empty($accessToken)) {
            return $this->error(HttpStatusCode::NO_CONTENT,"Access token not found.");
        }
        $result = "";
        switch ($action) {
            case self::ACTION_ADD:
                $result = WeChatAccessTools::addCustomer($accessToken,json_encode($message));
                break;
            case self::ACTION_UPDATE:
                $result = WeChatAccessTools::updateCustomer($accessToken,json_encode($message));
                break;
            case self::ACTION_DEL:
                $result = WeChatAccessTools::delCustomer($accessToken,json_encode($message));
                break;
        }

        if(empty($result)) {
            return $this->error(HttpStatusCode::EXPECTATION_FAILED,"WeChat return error.");
        }

        $wxError = json_decode($result);

        if(empty($wxError)) return $this->error(HttpStatusCode::EXPECTATION_FAILED,"WeChat return json error.");

        $this->success($wxError);
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
}