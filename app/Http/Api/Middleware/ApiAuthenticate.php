<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/25
 * Time: 18:32
 */

namespace App\Http\Api\Middleware;

use App\Common\Tools\HttpStatusCode;
use App\Common\Tools\RedisTools;
use App\Models\VendorAppSecret;
use Closure;
use Log;
use Mockery\Exception;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $app_key = $request->header('appkey');
        $nonce = $request->header('nonce');
        $cur_time = $request->header('curtime');
        $check_sum = $request->header('checksum');
        if (empty($app_key) || empty($check_sum)||empty($nonce)||empty($cur_time)) {
            return $this->error(HttpStatusCode::BAD_REQUEST,"参数错误");
        }
        $app_secret = $this->getSecret($app_key);
        if (empty($app_secret)) {
            return $this->error(HttpStatusCode::FORBIDDEN,"app secret not found.");
        }

        $now = time();
        $diff = $now - $cur_time;
        if ($diff>300) {
            return $this->error(HttpStatusCode::REQUEST_TIMEOUT,"time out.");
        }
        $check_sum_calc = sha1($app_secret.$nonce.$cur_time);
        if ($check_sum_calc != $check_sum) {
            return $this->error(HttpStatusCode::UNAUTHORIZED,"check sum failed!");
        }
        return $next($request);
    }
    private function error($code,$info="") {
        return response()->json(["message"=>$info],$code);
    }

    private function getSecret($appKey) {
        $secret = "";
        try {
            $secret = RedisTools::getAppSecret($appKey);
        } catch (\Exception $exception) {

        }
        if (empty($secret)) {
            //Get data form database and set it redis
            try {
                $secretModel = VendorAppSecret::where('app_key','=',$appKey)->first();
            } catch (\Exception $exception) {
                return "";
            }

            if(empty($secretModel)) {
                Log::error("find secret form db return null. for " . $appKey);
                return "";
            }

            if(empty($secretModel->app_secret)) {
                Log::error("find app secret form db return empty. for " . $appKey);
                return "";
            }
            $secret = $secretModel->app_secret;
            try {
                RedisTools::setAppSecret($appKey,$secret);
            } catch (\Exception $exception) {

            }


        }
        return $secret;
    }

}