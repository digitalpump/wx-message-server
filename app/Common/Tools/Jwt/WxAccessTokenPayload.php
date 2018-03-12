<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 09:33
 */

namespace App\Common\Tools\Jwt;


class WxAccessTokenPayload extends Payload
{
    /**
     * @var array
     */
    protected $defaultClaims = ['iat', 'nbf', 'jti'];
    function getDefaultClaims()
    {
        return $this->defaultClaims;
    }

}