<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 09:33
 */

namespace App\Common\Tools\Jwt;


class RefreshTokenPayload extends Payload
{
    /**
     * @var array
     */
    protected $defaultClaims = ['iat', 'exp', 'nbf', 'jti'];
    function getDefaultClaims()
    {
        // TODO: Implement getDefaultClaims() method.
    }

}