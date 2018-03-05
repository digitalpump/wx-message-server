<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 08:13
 */

namespace App\Http\FirstVersion\Controllers;


use App\Common\Tools\Jwt\PayloadFactory;
use Dotenv\Exception\ValidationException;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class JWTAuthController extends Controller
{

    public function emailLogin(Request $request) {
        try {
            $this->validate($request,[
                'email' => 'required|email|max:255',
                'password'=>'required',
            ]);

        } catch (ValidationException $e) {
            return $e->getResponse();
        }

        $secret_key = config('jwt.secret');
        $ttl = config('jwt.ttl');
        $refresh_ttl = config('jwt.refresh_ttl');
        $algo = config('jwt.algo');

        $payloadFactory = new PayloadFactory($ttl);

        $jwt_body = $payloadFactory->makePayloadWithUserId(2);
        //callback login ?


        $jwt_token = JWT::encode($jwt_body,$secret_key,$algo);

        JWT::decode($jwt_token,$secret_key,$algo);
        $key = "jeffrey_token";
        Redis::setex($key,60,$jwt_token);
        //app('redis')->put($key,$jwt_token);
        $refresh_token = Redis::get($key);
        return $this->onAuthorized($refresh_token,$jwt_token);

    }
    protected function onAuthorized($refreshToken,$token) {
        return $this->success(['refresh_token'=>$refreshToken,'token'=>$token],'Token generated success');
    }
}