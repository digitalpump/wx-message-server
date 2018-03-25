<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 08:13
 */

namespace App\Http\Controllers;

use App\Common\Tools\Jwt\JwtAuth;
use App\Common\Tools\Jwt\PayloadFactory;
use App\Common\Tools\JwtTokenTools;
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

     * App 重新授权登录接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
            $responseAccessToken = WxTokenTools::getAccessToken(app('WxConfig'),$wxCode);

            if (empty($responseAccessToken)) {
                return $this->error(HttpStatusCode::UNAUTHORIZED, "Get access token from weixin failed.");
            }
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

    /**
     * 微信小程序登录接口
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function wxMiniProgramLogin(Request $request)
    {
        $wxCode = $request->get('wx_code');

        $responseAccessToken = null;

        if (empty($wxCode)) {
            return $this->error(HttpStatusCode::BAD_REQUEST, "Code is required.");
        }
        try {

            $responseAccessToken = WxTokenTools::jscode2Session(app('WxConfig'),$wxCode);

            if (empty($responseAccessToken)) {
                return $this->error(HttpStatusCode::NO_CONTENT,"Jscode to session return null.");
            }

            if (!empty($responseAccessToken->errcode)) {  //微信服务器返回错误
                return $this->error(HttpStatusCode::UNAUTHORIZED, $responseAccessToken->errmsg);
            }


            //注册并登录，获取用户信息
            $ip = jf_get_ip();

            $result = UserTools::wxMiniProgramLogin($responseAccessToken,$ip);
            $uid = $result['uid'];

            if (empty($uid)) {
                return $this->error(HttpStatusCode::UNAUTHORIZED, "User not exist.");
            }

            //RedisTools::setWxSesssionKey($uid, $responseAccessToken->session_key);
            $tokens = JwtTokenTools::newWxLoginToken($uid,$responseAccessToken->openid);
            return $this->onAuthorized($tokens);

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


    /**
     * API 接口程序登录接口，
     *
     * @param Request $request
     *
     *          传入参数 ： appkey
     *                    nonce
     *                    curtime
     *                    checksum
     */
    public function apiWithAppkeyAndSecretLogin(Request $request) {

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

    /**
     *
     * 刷新token时除了解码传过来的刷新Token，还要检查旧的token（虽然已经访问过期），但是利用它来效验刷新token，增强安全性。
     * 旧的token的效验生生存期是24小时。
     * @param Request $request
     * @param $uid
     * @param boolean $checkExpireTime 是否检查旧token的有效期
     * @return bool
     */
    private function validateWithOldToken(Request $request,$uid,$checkExpireTime = false) {
        $jwtAuth = new JwtAuth();
        $jwtAuth->setJwtConfigure(app('JwtConfig'))->setRequest($request);
        try {
            $jwtAuth->setIgnores(['exp']);
            $payload = $jwtAuth->authenticate();
            if(empty($payload->sub)) return false;
            $tokenInRedis = RedisTools::getJwtToken($payload->sub);
            if (!empty($tokenInRedis)) {  //检查 Redis中是否有值

                if( $tokenInRedis!= $jwtAuth->getToken()) {  //说明新的token已经分发，当前token不能再用
                    Log::debug("@validateWithOldToken new token has published.");
                    return false;
                }
            }
            if ($checkExpireTime) {
                $passed_time = Carbon::now()->subHours(24)->timestamp;   //最大接受过期24小的旧token来换新token
                if (isset($payload->exp) && ($passed_time > $payload->exp)) {
                    Log::debug("@validateWithOldToken token expired");
                    return false;
                }
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
        $headerName = app('JwtConfig')->getRefreshTokenHeaderName();
        if (empty($headerName)) {
            return $this->error(HttpStatusCode::NOT_FOUND, 'Header name config not found.');
        }
        $jwtAuth = new JwtAuth();
        $jwtAuth->setJwtConfigure(app('JwtConfig'))->setRequest($request);

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

        $tokenInCache = RedisTools::getJwtRefreshToken($payload->sub);

        if ($tokenInCache!=$jwtAuth->getToken()) {
           return $this->error(HttpStatusCode::BAD_REQUEST,"Refresh token expired(a new refresh token has published).");
        }
        //TODO 可根据用户具体情况，决定是否开启对旧的token 过期的验证


        if (!$this->validateWithOldToken($request,$payload->sub,false)) {
            return $this->error(HttpStatusCode::UNAUTHORIZED, "验证用户ID失败");
        }

        $uid = $payload->sub;

        if (empty($payload->openid)) {
            // 生成新的普通账号密码登录token 和 refresh_token
            return $this->newNormallyToken($uid);
        } else {

            //生成新的微信登录token 和 refresh_token
            return $this->newWxLoginToken($uid, $payload->openid);
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
        $jwtAuth = new JwtAuth();
        $jwtAuth->setJwtConfigure(app('JwtConfig'));
        $token = $this->generateNewToken($jwtAuth, $uid);
        $refresh_token = $this->generateNewRefreshToken($jwtAuth, $uid);
        return $this->onAuthorized(['token' => app('JwtConfig')->getAuthMethod() . $token
            , 'refresh_token' => app('JwtConfig')->getAuthMethod() . $refresh_token]);
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
        $jwtAuth = new JwtAuth();
        $jwtAuth->setJwtConfigure(app('JwtConfig'));
        $token = $this->generateNewToken($jwtAuth, $uid);
        $refresh_token = $this->generateNewWxRefreshToken($jwtAuth, $uid, $openid);
        return $this->onAuthorized(['token' => app('JwtConfig')->getAuthMethod() . $token
            , 'refresh_token' => app('JwtConfig')->getAuthMethod() . $refresh_token]);

    }


    /**
     * 生成新的token 接口
     * @param JwtAuth $jwtAuth
     * @param $uid
     * @return string
     */
    private function generateNewToken(JwtAuth $jwtAuth, $uid)
    {
        $token_ttl = app('JwtConfig')->getTokenTtl();
        $token =  $jwtAuth->encode(function () use ($uid,$token_ttl) {
            return PayloadFactory::make()->setTTL($token_ttl)->buildClaims(['sub' => $uid])->getClaims();
        },false);
        if(empty($token)) return "";
        RedisTools::setJwtToken($uid,$token_ttl,$token);
        return $token;
    }

    /**
     * 生成新的刷新token接口
     * @param JwtAuth $jwtAuth
     * @param $uid
     * @return string
     */
    private function generateNewRefreshToken(JwtAuth $jwtAuth, $uid)
    {
        $refresh_ttl = app('JwtConfig')->getRefreshTokenTtl();
        $refresh_delay = app('JwtConfig')->getRefreshTokenDelay();
        $token = $jwtAuth->encode(function () use ($uid, $refresh_ttl,$refresh_delay) {
            $nbf = Carbon::now()->addMinutes($refresh_delay)->timestamp;
            return PayloadFactory::make()->setTTL($refresh_ttl)->buildClaims(['nbf' => $nbf, 'sub' => $uid])->getClaims();
        },true);

        if(empty($token)) return $token;
        RedisTools::setJwtRefreshToken($uid,$refresh_ttl,$token);
        return $token;
    }

    /**
     * 生成新的微信刷新token接口
     * @param JwtAuth $jwtAuth
     * @param $uid
     * @param $openid
     * @return string
     */
    private function generateNewWxRefreshToken(JwtAuth $jwtAuth, $uid, $openid)
    {
        $refresh_ttl = app('JwtConfig')->getRefreshTokenTtl();
        $refresh_delay = app('JwtConfig')->getRefreshTokenDelay();

        $token = $jwtAuth->encode(function () use ($uid, $openid, $refresh_ttl,$refresh_delay) {
            $nbf = Carbon::now()->addMinutes($refresh_delay)->timestamp;
            return PayloadFactory::make()->setTTL($refresh_ttl)->buildClaims(['openid' => $openid, 'nbf' => $nbf, 'sub' => $uid])->getClaims();
        },true);
        if(empty($token)) return $token;
        RedisTools::setJwtRefreshToken($uid,$refresh_ttl,$token);
        return $token;
    }


    protected function onAuthorized($tokens)
    {
        return $this->success($tokens, 'Authentication success');
    }
}