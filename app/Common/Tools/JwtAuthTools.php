<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 21:11
 */

namespace App\Common\Tools;


use App\Common\Tools\Configure\JwtConfigure;
use App\Common\Tools\Jwt\JwtAuth;


class JwtAuthTools
{
    private $jwtAuth;
    private $jwtConfigure;
    /*
     *
     */
    public function __construct(JwtConfigure $jwtConfigure)
    {
        $this->jwtConfigure = $jwtConfigure;
        $this->jwtAuth = (new JwtAuth())->setJwtConfigure($jwtConfigure);
    }

    public function newToken(callable $callback) {
        $payload = call_user_func($callback);
        if(empty($payload)) {
            return "";
        }
        return $this->jwtAuth->encode($payload);
    }

    public function newRefreshToken(callable $callback) {
        $payload = call_user_func($callback);
        if(empty($payload)) {
            return "";
        }
        return $this->jwtAuth->encode($payload,true);
    }

    public function getAuthorizationMethod() {
        return $this->jwtConfigure->getAuthMethod();
    }

}