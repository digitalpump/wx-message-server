<?php
namespace App\Common\Tools;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 09:02
 */
class JWTAuth
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    protected $token;

    protected $ttl = 60;

    protected $refresh_ttl = 60;

    protected $secret = "V9JeffreysbVgqihxBUoBN4iSUXwUDwJE7";

    protected $algo;

    protected $auth_method = "bearer";

    public function __construct(Request $request)
    {
        $this->request = $request;

        $secret = config('jwt.secret');
        if(!empty($secret)) $this->secret = $secret;

        $ttl = config('jwt.ttl');
        if(!empty($ttl)) $this->ttl = intval($ttl);

        $refresh_ttl = config('jwt.refresh_ttl');
        if(!empty($refresh_ttl)) $this->refresh_ttl = $refresh_ttl;

        $algo = config('jwt.algo');
        if(!empty($algo)) $this->algo = $algo;

        $method = config('jwt.auth_method');
        if(!empty($method)) $this->auth_method = $method;
    }

    /**
     * Set the request instance.
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function authenticate() {

        if(!$this->validateAuthorizationHeader()) {
            throw new \UnexpectedValueException("Authorization header not found");
        }

        $token = $this->parseAuthorizationHeader();

        if(empty($token)) {
            throw  new \UnexpectedValueException("Empty token");
        }
        $payload = null;

        if (empty($this->algo)) {
            $payload = JWT::decode($token,$this->secret);
        } else {
            $payload = JWT::decode($token,$this->secret,$this->algo);
        }

        if(empty($payload)) {
            throw new \UnexpectedValueException("Empty payload");
        }
        if(empty($payload->sub)) {
            throw new \UnexpectedValueException("Empty sub claim");
        }
        return $payload->sub;
    }

    private function validateAuthorizationHeader() {
        if(empty($this->request)) return false;
        $authHeader = $this->request->headers->get('authorization');
        if(empty($authHeader)) return false;

        if (Str::startsWith(strtolower($authHeader), $this->getAuthorizationMethod())) {
            return true;
        }
        return false;
    }

    /**
     * Parse JWT from the authorization header.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function parseAuthorizationHeader()
    {
        return trim(str_ireplace($this->getAuthorizationMethod(), '', $this->request->header('authorization')));
    }

    /**
     * Get the providers authorization method.
     *
     * @return string
     */
    public function getAuthorizationMethod()
    {
        return $this->auth_method;
    }

    public function makePayload($user_id,array $customClaims=[]) {
        array_merge($customClaims,['sub'=>$user_id]);
    }
}