<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 09:34
 */

namespace App\Common\Tools\Jwt;


interface IPayload
{
    public function getClaims();

}