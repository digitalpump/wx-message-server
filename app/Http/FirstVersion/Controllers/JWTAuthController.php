<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 08:13
 */

namespace App\Http\FirstVersion\Controllers;


use Dotenv\Exception\ValidationException;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Carbon\Carbon;
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

        //callback login ?
        $jwt_body = array(
            "user_id" => 1,
            "user_name" => 'jeffrey',
            "email" => $request->get('email'),
            "aud"=>"chronos",
            "iat" => Carbon::now(),
        );

        $jwt_token = JWT::encode($jwt_body,$secret_key,$algo);
        return $this->onAuthorized("XXXYYYY",$jwt_token);

    }
    protected function onAuthorized($refreshToken,$token) {
        return $this->success(['refresh_token'=>$refreshToken,'token'=>$token],'Token generated success');
    }
}