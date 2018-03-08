<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/8
 * Time: 07:47
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class JwtUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'JwtUser';
    }

}