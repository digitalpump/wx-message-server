<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/16
 * Time: 21:44
 */

namespace App\Common\Tools\Jwt;


class JwtConfigure
{

    private $token;


    private $secret = "V9JeffreysbVgqihxBUoBN4iSUXwUDwJE7";

    private $refresh_secret = "V6JeffreysbVgqihxBUoBN4iSUXwUDwJE8";

    private $algo = "HS256";

    private $auth_method = "bearer";

    private $token_ttl;

    private $refresh_ttl;

    public function __construct()
    {
        $secret = config('jwt.secret');
        if(!empty($secret)) $this->secret = $secret;

        $refresh_secret = config('jwt.refresh_secret');
        if(!empty($refresh_secret)) $this->refresh_secret = $refresh_secret;

        $ttl = config('jwt.ttl');
        if(!empty($ttl)) $this->token_ttl = intval($ttl);

        $refresh_ttl = config('jwt.refresh_ttl');
        if(!empty($refresh_ttl)) $this->refresh_ttl = $refresh_ttl;

        $algo = config('jwt.algo');
        if(!empty($algo)) $this->algo = $algo;

        $method = config('jwt.auth_method');
        if(!empty($method)) $this->auth_method = $method;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed|string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return mixed|string
     */
    public function getRefreshSecret()
    {
        return $this->refresh_secret;
    }

    /**
     * @return mixed|string
     */
    public function getAlgo()
    {
        return $this->algo;
    }

    /**
     * @return mixed|string
     */
    public function getAuthMethod()
    {
        return $this->auth_method;
    }

    /**
     * @return int
     */
    public function getTokenTtl()
    {
        return $this->token_ttl;
    }

    /**
     * @return mixed
     */
    public function getRefreshTtl()
    {
        return $this->refresh_ttl;
    }




}