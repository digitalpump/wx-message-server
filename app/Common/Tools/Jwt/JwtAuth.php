<?php
namespace App\Common\Tools\Jwt;

use App\Common\Tools\Jwt\Firebase\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Common\Tools\Configure\JwtConfigure;
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

    protected $jwtConfigure;

    protected $ignores = [];

    private $token;

    const DEFAULT_HEADER_NAME = "Authorization";

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

    public function setJwtConfigure(JwtConfigure $jwtConfigure) {
        $this->jwtConfigure = $jwtConfigure;
        return $this;
    }

    /**
     * @return array
     */
    public function getIgnores()
    {
        return $this->ignores;
    }

    /**
     * @param array $ignores
     */
    public function setIgnores($ignores)
    {
        $this->ignores = $ignores;
    }


    public function authenticate($headerName=self::DEFAULT_HEADER_NAME) {

        if(!$this->validateAuthorizationHeader($headerName)) {
            throw new AuthHeaderNotFoundException("$headerName header not found");
        }

        $this->token = $this->parseAuthorizationHeader($headerName);

        if(empty($this->token)) {
            throw  new AuthTokenEmptyException("Empty token");
        }
        $payload = null;
        try {
            $secret = $headerName==self::DEFAULT_HEADER_NAME?$this->getJwtConfigure()->getSecret():$this->getJwtConfigure()->getRefreshSecret();
            $algo = $this->getJwtConfigure()->getAlgo();
            if (empty($algo)) {
                $payload = JWT::decode($this->token, $secret,[],$this->getIgnores());
            } else {
                $payload = JWT::decode($this->token, $secret, [$algo],$this->getIgnores());
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

    private function validateAuthorizationHeader($headerName) {
        if(empty($this->request)) return false;
        $authHeader = $this->request->headers->get($headerName);
        if(empty($authHeader)) return false;

        if (Str::startsWith(strtolower($authHeader), $this->getJwtConfigure()->getAuthMethod())) {
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
    protected function parseAuthorizationHeader($headerName)
    {
        return trim(str_ireplace($this->getJwtConfigure()->getAuthMethod(), '', $this->request->header($headerName)));
    }


    public function encode(callable $callback,$refreshToken=false) {
        $payload = call_user_func($callback);
        if (empty($payload)) {
            return "";
        }
        $secret = $refreshToken?$this->getJwtConfigure()->getRefreshSecret():$this->getJwtConfigure()->getSecret();
        $algo = $this->getJwtConfigure()->getAlgo();
        if(empty($algo)) return JWT::encode($payload,$secret);
        return JWT::encode($payload,$secret,$algo);

    }


    public function getToken() {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getJwtConfigure()
    {
        return $this->jwtConfigure;
    }




}