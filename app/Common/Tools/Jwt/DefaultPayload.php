<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 09:31
 */

namespace App\Common\Tools\Jwt;


class DefaultPayload extends Payload
{
    /**
     * @var array
     */
    protected $defaultClaims = ['iat', 'exp', 'nbf', 'jti'];

    function getDefaultClaims()
    {
        return $this->defaultClaims;
    }


}