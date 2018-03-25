<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/25
 * Time: 13:22
 */

namespace App\Http\Api\Controllers;

use App\Common\Tools\HttpStatusCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Log;
class MessageApiController extends Controller
{

    /**
     *
     *
     * @param Request $request
     */
    public function sendMessage(Request $request) {
        $obj = $request->json()->all();
        if(empty($obj)) return $this->error(HttpStatusCode::BAD_REQUEST,"json body is empty.");

        $app_key = $request->header('appkey');
        //Log::debug(json_encode($obj));
        if(empty($obj['message']) || empty($obj['appid'])) return $this->error(HttpStatusCode::BAD_REQUEST,"message empty.");

        //检查appid 是否存在并属于该用户 with app_key

        $key = env('MESSAGE_POOL_KEY_RPEFIX') . $obj['appid'];
        $result =  Redis::rpush($key,json_encode($obj['message']));
        if (empty($request)) return $this->error(HttpStatusCode::NOT_MODIFIED,"Send message failed.");
        return $this->success($result);
    }
}