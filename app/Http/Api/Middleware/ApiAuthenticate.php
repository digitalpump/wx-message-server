<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/25
 * Time: 18:32
 */

namespace App\Http\Api\Middleware;

use App\Common\Tools\HttpStatusCode;
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
            return $this->error(HttpStatusCode::FORBIDDEN,"Secret need.");
        }
        $now = time();
        $diff = $now - $cur_time;
        if ($diff>300) {
            return $this->error(HttpStatusCode::REQUEST_TIMEOUT,"time out.");
        }
        $check_sum_calc = sha1($app_secret.$nonce.$cur_time);
        if ($check_sum_calc != $check_sum) {
            return $this->error(HttpStatusCode::UNAUTHORIZED,"检验失败!");
        }
        return $next($request);
    }
    private function error($code,$info="") {
        return response()->json(["message"=>$info],$code);
    }

    private function getSecret($appKey) {
        return "";
    }

}