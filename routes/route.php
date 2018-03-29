<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 07:25
 */
$app->router->group(['prefix' => 'api/v1',
    'namespace' => 'App\Http\Api\Controllers',
], function ($router) {
    require_once __DIR__ . '/api_v1.php';
});

$app->router->group(['prefix'=>'api/v1','namespace' => 'App\Http\Api\Controllers',
    'middleware' => ['api.auth']
],function ($router){
    require_once __DIR__ . '/api_v1_auth.php';
});

$app->router->group(['prefix'=>'weixin/v1'
    ,'namespace' => 'App\Http\Api\Controllers',

],function ($router){
    require_once __DIR__ . '/wx_api.php';
});