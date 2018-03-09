<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 08:13
 */

namespace App\Http\Controllers;


use App\Common\Tools\Jwt\JwtAuth;

use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use App\Common\Tools\HttpStatusCode;
use App\Common\Tools\Jwt\AuthHeaderNotFoundException;
use App\Common\Tools\Jwt\AuthTokenEmptyException;
use App\Common\Tools\Jwt\SubClaimNotFoundException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Log;
class JWTAuthController extends Controller
{

    private $testUserDeviceId = "IJysdfkyjklkhyiiii28";
    public function emailLogin(Request $request) {

        try {
            $this->validate($request,[
                'email' => 'required|email|max:255',
                'password'=>'required',
            ]);

        } catch (ValidationException $e) {
            return $e->getResponse();
        }

        $uid = 2;

        $tokens = $this->generateNewToken(new JwtAuth($request),$uid,$this->testUserDeviceId);
        return $this->onAuthorized($tokens);

    }

    public function refreshToken(Request $request) {
        $jwtAuth = new JwtAuth($request);
        try {
            $payload = $jwtAuth->authenticate();
        } catch (ExpiredException $e) {
            return $this->error(HttpStatusCode::REQUEST_TIMEOUT,$e->getMessage());
        } catch (SignatureInvalidException $exception) {
            return $this->error(HttpStatusCode::UNAUTHORIZED,$exception->getMessage());
        } catch (BeforeValidException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        } catch (AuthHeaderNotFoundException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        } catch (AuthTokenEmptyException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        } catch (SubClaimNotFoundException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        } catch (\UnexpectedValueException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        }
        if (empty($payload->did)) {
            return $this->error(HttpStatusCode::BAD_REQUEST,"Device id unknown");
        }
        $deviceId = $payload->did;
        if($deviceId == $this->testUserDeviceId) {
            $uid = $payload->sub;
            $tokens = $this->generateNewToken($jwtAuth,$uid,$deviceId);
            return $this->onAuthorized($tokens);
        } else {
            return $this->error(HttpStatusCode::UNAUTHORIZED,"Device error.");
        }

    }

    private function generateNewToken($jwtAuth,$uid,$deviceId) {

        $jwt_token = $jwtAuth->newToken($uid);

        $refresh_token = $jwtAuth->newRefreshToken($uid,$deviceId);

        $key = "refresh_token_user_".$uid;
        Redis::setex($key,$jwtAuth->getRefreshTtl()*60,$refresh_token);
        return ['token'=>$jwt_token,'refresh_token'=>$refresh_token];
    }

    protected function onAuthorized($tokens) {
        return $this->success($tokens,'Authentication success');
    }
}