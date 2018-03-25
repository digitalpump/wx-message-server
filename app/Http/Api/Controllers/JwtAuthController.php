<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/25
 * Time: 12:03
 */

namespace App\Http\Api\Controllers;


use App\Common\Tools\HttpStatusCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JwtAuthController extends Controller
{

    /**
     * @param Request $request
     *   传入参数：
     *          appkey
     *          nonce 随机串
     *          curtime 当前时间
     *          checksum 校验和
     */
    public function apiLogin(Request $request) {
        $app_key = $request->input('appkey');
        $nonce = $request->input('nonce');
        $cur_time = $request->input('curtime');
        $check_sum = $request->input('checksum');
        if (empty($app_key) || empty($check_sum)||empty($nonce)||empty($cur_time)) {
            return $this->error(HttpStatusCode::BAD_REQUEST,"参数错误");
        }
        $app_secret = $this->getSecret($app_key);
        if (empty($app_secret)) {
            return $this->error(HttpStatusCode::FORBIDDEN,"Secret need.");
        }
        $now = time();
        $diff = $now - $cur_time;
        if($diff>300) {
            return $this->error(HttpStatusCode::REQUEST_TIMEOUT,"");
        }
        $check_sum_calc = sha1($app_secret.$nonce.$cur_time);
        if ($check_sum_calc != $check_sum) {
            return $this->error(HttpStatusCode::UNAUTHORIZED,"检验失败!");
        }
        //TODO make JWTAuth tokens;
        
    }

}