<?php
namespace App\Common\Tools\Jwt;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 09:02
 */
class JwtAuth
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    protected $token;


    protected $secret = "V9JeffreysbVgqihxBUoBN4iSUXwUDwJE7";

    protected $refresh_secret = "V6JeffreysbVgqihxBUoBN4iSUXwUDwJE8";

    protected $algo = "HS256";

    protected $auth_method = "bearer";



    /**
     * @var array
     */


    public function __construct(Request $request = null)
    {
        $this->request = $request;

        $secret = config('jwt.secret');
        if(!empty($secret)) $this->secret = $secret;

        $refresh_secret = config('jwt.refresh_secret');
        if(!empty($refresh_secret)) $this->refresh_secret = $refresh_secret;

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

    public function authenticate($headerName="authorization") {

        if(!$this->validateAuthorizationHeader($headerName)) {
            throw new AuthHeaderNotFoundException("Authorization header not found");
        }

        $this->token = $this->parseAuthorizationHeader($headerName);

        if(empty($this->token)) {
            throw  new AuthTokenEmptyException("Empty token");
        }
        $payload = null;
        try {
            $secret = $headerName=='authorization'?$this->secret:$this->refresh_secret;
            if (empty($this->algo)) {

                $payload = JWT::decode($this->token, $secret);
            } else {
                $payload = JWT::decode($this->token, $secret, [$this->algo]);
            }
        } catch (\DomainException $exception) {
            throw new \UnexpectedValueException($exception->getMessage());
        }


        if(empty($payload)) {
            throw new \UnexpectedValueException("Empty payload");
        }
        if(empty($payload->sub)) {
            throw new SubClaimNotFoundException("Empty sub claim");
        }
        return $payload;
    }

    private function validateAuthorizationHeader($headerName="authorization") {
        if(empty($this->request)) return false;
        $authHeader = $this->request->headers->get($headerName);
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
    protected function parseAuthorizationHeader($headerName="authorization")
    {
        return trim(str_ireplace($this->getAuthorizationMethod(), '', $this->request->header($headerName)));
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


    public function encode($payload,$refreshToken=false) {
        if ($refreshToken) {
            return JWT::encode($payload,$this->refresh_secret,$this->algo);
        } else {
            return JWT::encode($payload,$this->secret,$this->algo);
        }
    }


    public function getToken() {
        return $this->token;
    }


}