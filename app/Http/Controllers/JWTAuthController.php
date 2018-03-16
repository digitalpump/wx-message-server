<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 08:13
 */

namespace App\Http\Controllers;


use App\Common\Tools\Jwt\JwtAuth;

use App\Common\Tools\Jwt\JwtConfigure;
use App\Common\Tools\Jwt\PayloadFactory;
use App\Common\Tools\JwtAuthTools;
use App\Common\Tools\RedisTools;
use App\Common\Tools\UserTools;
use App\Common\Tools\WxTokenTools;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Carbon\Carbon;
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

    /**
     * 微信App 或者小程序登录流程
     *
     * 1，客户端自已向微信发起并获取code
     * 2, 用 code 向自己的服务器发起登录请求
     * 3，自己的服务器通过code 向微信获取access_token,refresh_token 和 openid,unionid 等
     *      https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code
     *
     *    自己的服务器根据openid查寻或者创建本地用户信息
     *      登录成功返回：微信（access_token,openid)
     *           用户ID
     *           用户本站认证token 和 refresh_token
     * 4，客户端通过 openid 和 access_token 向微信获取用户详细信息
     *     https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID
     *
     * 5，客户端将获取到的用户信息注册到自己的服务器（有必要的话）
     *
     *
     *
     *
     * App 重新授权登录接口
     */
    public function wxLogin(Request $request)
    {
        //1, Get code from request
        $wxCode = $request->get('wx_code');

        $responseAccessToken = null;

        if (empty($wxCode)) {
            return $this->error(HttpStatusCode::BAD_REQUEST, "Code is required.");
        }
        try {
            $responseAccessToken = $this->wxGetAccessTokenByCode($wxCode);

            if (!empty($responseAccessToken->errcode)) {  //微信服务器返回错误
                return $this->error(HttpStatusCode::UNAUTHORIZED, $responseAccessToken->errmsg);
            }

            //注册并登录，获取用户信息
            list($uid, $role, $status) = UserTools::wxLogin($responseAccessToken);

            if (empty($uid)) {
                return $this->error(HttpStatusCode::UNAUTHORIZED, "User not exist.");
            }


            return $this->newWxLoginToken($uid, $responseAccessToken->openid);
            /*
             *  $uid = UserTools->wxLogin($responseAccessToken)
             *
             */

        } catch (\UnexpectedValueException $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->error(HttpStatusCode::INTERNAL_SERVER_ERROR, $exception->getMessage());
        }


        // return $this->wxGetAccessTokenByToken($wxAccessToken,$openid);
    }

    public function wxMiniProgramLogin(Request $request)
    {
        $wxCode = $request->get('wx_code');


        $responseAccessToken = null;

        if (empty($wxCode)) {
            return $this->error(HttpStatusCode::BAD_REQUEST, "Code is required.");
        }
        try {
            $responseAccessToken = $this->wxJsCodeToSession($wxCode);

            if (!empty($responseAccessToken->errcode)) {  //微信服务器返回错误
                return $this->error(HttpStatusCode::UNAUTHORIZED, $responseAccessToken->errmsg);
            }


            //注册并登录，获取用户信息
            $result = UserTools::wxMiniProgramLogin($responseAccessToken);
            $uid = $result['uid'];


            if (empty($uid)) {
                return $this->error(HttpStatusCode::UNAUTHORIZED, "User not exist.");
            }

            RedisTools::setWxSesssionKey($uid, $responseAccessToken->session_key);

            return $this->newWxLoginToken($uid, $responseAccessToken->openid);
            /*
             *  $uid = UserTools->wxLogin($responseAccessToken)
             *
             */

        } catch (\UnexpectedValueException $exception) {
            return $this->error($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->error(HttpStatusCode::INTERNAL_SERVER_ERROR, $exception->getMessage());
        }

    }


    private function authFromWeixinByToken(Request $request)
    {

    }

    /**
     *
     *
     * 客户端用access_token 刷新并登录  （假设用户已经获得授权，则下次登录时只需要验证access_token是否有效，无效则重新获取授权，有效则无需重新获得授权。）
     * @param Request $request
     */
    private function wxGetAccessTokenByToken($access_token, $openid)
    {
        //检查access_token 是否有效
        //从数据库中查出 refresh_token
        //用refresh_token 刷新token

    }

    private function wxGetAccessTokenByCode($code)
    {
        $appid = config('weixin.appid');
        $secret = config('weixin.secret');
        if (empty($appid) || empty($secret)) {
            throw new \UnexpectedValueException("Weixin configure not found", HttpStatusCode::EXPECTATION_FAILED);
        }
        $body = WxTokenTools::getAccessToken($appid, $secret, $code);
        if (empty($body)) {
            throw new \UnexpectedValueException("Get access token server response body empty.", HttpStatusCode::NO_CONTENT);
        }
        return $body;

    }

    private function wxJsCodeToSession($code)
    {
        $appid = config('weixin.appid');
        $secret = config('weixin.secret');
        if (empty($appid) || empty($secret)) {
            throw new \UnexpectedValueException("Weixin configure not found", HttpStatusCode::EXPECTATION_FAILED);
        }
        $body = WxTokenTools::jscode2Session($appid, $secret, $code);
        if (empty($body)) {
            throw new \UnexpectedValueException("Get access token server response body empty.", HttpStatusCode::NO_CONTENT);
        }
        return $body;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailLogin(Request $request)
    {

        try {
            $this->validate($request, [
                'email' => 'required|email|max:255',
                'password' => 'required'
            ]);

        } catch (ValidationException $e) {
            return $e->getResponse();
        }

        //UserTools->emailLogin();
        $uid = 2;

        return $this->newNormallyToken($uid);

    }

    private function validateWithOldToken(Request $request,$uid) {
        $jwtAuth = new JwtAuth();
        $jwtAuth->setJwtConfigure(new JwtConfigure())->setRequest($request);
        try {
            $jwtAuth->setIgnores(['exp']);
            $payload = $jwtAuth->authenticate();
            // Check if this token has expired.
            Log::debug("exp=".$payload->exp);
            if (isset($payload->exp) && (Carbon::yesterday()->timestamp >= $payload->exp)) {
                Log::debug("@checkOldToken token expired");
                return false;
            }
            if ($uid!=$payload->sub) return false;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }
    /**
     * 刷新token接口
     *
     * @param Request $request
     *      header "Authorization" 传入旧token
     *      header "RefreshToken" 传入刷新token
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {

        $headerName = config('jwt.refresh_header_name');
        if (empty($headerName)) {
            return $this->error(HttpStatusCode::NOT_FOUND, 'Header name config not found.');
        }
        $jwtAuth = new JwtAuth();
        $jwtAuth->setJwtConfigure(new JwtConfigure())->setRequest($request);
        try {
            $payload = $jwtAuth->authenticate($headerName);
        } catch (ExpiredException $e) {
            return $this->error(HttpStatusCode::REQUEST_TIMEOUT, $e->getMessage());
        } catch (SignatureInvalidException $exception) {
            return $this->error(HttpStatusCode::UNAUTHORIZED, $exception->getMessage());
        } catch (BeforeValidException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST, $exception->getMessage());
        } catch (AuthHeaderNotFoundException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST, $exception->getMessage());
        } catch (AuthTokenEmptyException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST, $exception->getMessage());
        } catch (SubClaimNotFoundException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST, $exception->getMessage());
        } catch (\UnexpectedValueException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST, $exception->getMessage());
        }


        if (empty($payload->sub)) {
            return $this->error(HttpStatusCode::BAD_REQUEST, "User id not found");
        }


        if (!$this->validateWithOldToken($request,$payload->sub)) {
            return $this->error(HttpStatusCode::UNAUTHORIZED, "验证用户ID失败");
        }

        $uid = $payload->sub;

        //TODO 检查用户权限是否还在？by uid or by openid

        if (empty($payload->openid)) {
            // 生成新的普通账号密码登录token 和 refresh_token
            return $this->newNormallyToken($uid);
        } else {

            //读取用户信息，获得微信刷新token ,用户权限等
            $wxRefreshToken = UserTools::getWxRefreshToken($payload->openid);
            if (empty($wxRefreshToken)) {
                return $this->error(HttpStatusCode::GONE, "Weixin refresh token gone.");
            }

            //向微信服务发出刷新token请求
            $wxResponse = WxTokenTools::refreshAccessToken($payload->openid, $wxRefreshToken);

            if (empty($wxResponse)) return $this->error(HttpStatusCode::INTERNAL_SERVER_ERROR, "Weixin server error.");

            if (!empty($wxResponse->errcode)) return $this->error(HttpStatusCode::GONE, $wxRefreshToken->errmsg);

            UserTools::updateWxTokens($wxResponse->openid, $wxResponse->access_token, $wxResponse->refresh_token);

            //生成新的微信登录token 和 refresh_token
            return $this->newWxLoginToken($uid, $wxResponse->openid);
        }


    }

    /**
     * 返回普通登录token
     * @param $uid
     * @param $did
     * @return \Illuminate\Http\JsonResponse
     */
    private function newNormallyToken($uid)
    {
        $jwtAuthTools = new JwtAuthTools();
        $token = $this->generateNewToken($jwtAuthTools, $uid);
        $refresh_token = $this->generateNewRefreshToken($jwtAuthTools, $uid);
        return $this->onAuthorized(['token' => $jwtAuthTools->getAuthorizationMethod() . $token
            , 'refresh_token' => $jwtAuthTools->getAuthorizationMethod() . $refresh_token]);
    }


    /**
     * 返回微信登录 token
     * @param $uid
     * @param $did
     * @param $openid
     * @return \Illuminate\Http\JsonResponse
     */
    private function newWxLoginToken($uid, $openid)
    {
        $jwtAuthTools = new JwtAuthTools();
        $token = $this->generateNewToken($jwtAuthTools, $uid);
        $refresh_token = $this->generateNewWxRefreshToken($jwtAuthTools, $uid, $openid);
        return $this->onAuthorized(['token' => $jwtAuthTools->getAuthorizationMethod() . $token
            , 'refresh_token' => $jwtAuthTools->getAuthorizationMethod() . $refresh_token]);

    }


    private function generateNewToken($jwtAuthTools, $uid)
    {
        $token_ttl = config('app.token_ttl', 60);
        $token =  $jwtAuthTools->newToken(function () use ($uid,$token_ttl) {
            return PayloadFactory::make()->setTTL($token_ttl)->buildClaims(['sub' => $uid])->getClaims();
        });
        RedisTools::setToken($uid,$token_ttl,$token);
        return $token;
    }

    private function generateNewRefreshToken($jwtAuthTools, $uid)
    {
        $refresh_ttl = config('app.refresh_token_ttl', 2160);
        return $jwtAuthTools->newRefreshToken(function () use ($uid, $refresh_ttl) {
            $delay = config('app.refresh_token_delay', 5);

            $nbf = Carbon::now()->addMinutes($delay)->timestamp;
            return PayloadFactory::make()->setTTL($refresh_ttl)->buildClaims(['nbf' => $nbf, 'sub' => $uid])->getClaims();
        });

    }

    private function generateNewWxRefreshToken($jwtAuthTools, $uid, $openid)
    {
        $refresh_ttl = config('app.refresh_token_ttl', 2160);
        return $jwtAuthTools->newRefreshToken(function () use ($uid, $openid, $refresh_ttl) {
            $delay = config('app.refresh_token_delay', 5);

            $nbf = Carbon::now()->addMinutes($delay)->timestamp;
            return PayloadFactory::make()->setTTL($refresh_ttl)->buildClaims(['openid' => $openid, 'nbf' => $nbf, 'sub' => $uid])->getClaims();
        });

    }


    protected function onAuthorized($tokens)
    {
        return $this->success($tokens, 'Authentication success');
    }
}