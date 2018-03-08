<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 07:25
 */
$app->router->group(['prefix' => 'api/v1',
    'namespace' => 'App\Http\FirstVersion\Controllers',
], function ($router) {
    require_once __DIR__ . '/api_v1.php';
});
$app->router->group(['prefix'=>'api/v1','namespace' => 'App\Http\FirstVersion\Controllers',
    'middleware' => ['jwt.auth']
],function ($router){
    require_once __DIR__ . '/api_v1_auth.php';
});