<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 21:11
 */

namespace App\Common\Tools;


use App\Common\Tools\Jwt\JwtAuth;

class JwtAuthTools
{
    private $jwtAuth;
    /*
     *
     */
    public function __construct()
    {
        $this->jwtAuth = new JwtAuth();
    }

    public function newToken(callable $callback) {
        $payload = call_user_func($callback);
        if(empty($payload)) {
            return "";
        }
        return $this->jwtAuth->encode($payload);
    }

    public function getAuthorizationMethod() {
        return $this->jwtAuth->getAuthorizationMethod();
    }

}