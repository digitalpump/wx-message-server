<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:58
 */
$router->get('/service','WeixinApiController@testOfficialServe');
$router->post('/service','WeixinApiController@testOfficialServe');
$router->get('/serve/miniprogram','WeixinApiController@miniProgramServe');
$router->post('/serve/miniprogram','WeixinApiController@miniProgramServe');
$router->get('/serve/foolisholdcodingman','WeixinApiController@officialServe');
$router->post('/serve/foolisholdcodingman','WeixinApiController@officialServe');