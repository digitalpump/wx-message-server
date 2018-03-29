<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:59
 */

namespace App\Http\Api\Middleware;

use App\Common\Tools\CommonTools;
use App\Common\Tools\HttpStatusCode;
use Closure;

use Log;
class WeixinApiAuthenticate
{
    public function handle($request, Closure $next){

        //$nonce = $request->input('nonce');
        //$timestamp = $request->input('timestamp');
        //$signature = $request->input('signature');
        $token = env('WECHAT_OFFICIAL_ACCOUNT_TOKEN','');

        /*if (empty($token) || empty($signature)||empty($nonce)||empty($timestamp)) {
            Log::error("参数错误，请检查配置！token=".$token." and nonce=".$nonce);
            return $this->error(HttpStatusCode::BAD_REQUEST,"参数错误");
        }
        $sha1 = CommonTools::getSHA1($token,$timestamp,$nonce);
        if($sha1!=$signature) {
            Log::error("签名不正确！sign from weixin=".$signature);
        }*/
        return $next($request);
    }
    private function error($code,$info="") {

        return response($info,$code);
    }
}