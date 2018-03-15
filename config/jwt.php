<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 08:22
 */
return [
    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this, as it will be used to sign your tokens.
    | A helper command is provided for this: `php artisan jwt:generate`
    |
    */

    'secret' => env('JWT_SECRET', 'z4BfGXKq09Pd4vmo6pOCT2qDkdLfCiL3Vt6khtA4bKIFRSYcdlTiwmCMxZDZ0b35'),


    'refresh_secret' => env('JWT_REFRESH_SECRET','UQ90hNCgq849p8gK6777X4jeH2uz6X17xUexD20STfg3B9BE3EnLuPHnx1Y4SxqE'),

    /*
    |--------------------------------------------------------------------------
    | JWT time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    | Defaults to 1 hour
    |
    */

    'ttl' => 60,

    /*
    |--------------------------------------------------------------------------
    | Refresh time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | within. I.E. The user can refresh their token within a 2 week window of
    | the original token being created until they must re-authenticate.
    | Defaults to 2 weeks
    |
    */

    'refresh_ttl' => 20160,

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm that will be used to sign the token.
    |
    | See here: https://github.com/namshi/jose/tree/2.2.0/src/Namshi/JOSE/Signer
    | for possible values
    |
    */

    'algo' => 'HS256',


    'auth_method'=>'jeffr',

    'refresh_header_name' => 'RefreshToken',


];