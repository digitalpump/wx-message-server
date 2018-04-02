<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:58
 */
$router->get('/serve/test1','WeixinServeApiController@testOfficialServe');
$router->post('/serve/test1','WeixinServeApiController@testOfficialServe');
$router->get('/serve/test2','WeixinServeApiController@test2OfficialServe');
$router->post('/serve/test2','WeixinServeApiController@test2OfficialServe');
$router->get('/serve/miniprogram','WeixinServeApiController@miniProgramServe');
$router->post('/serve/miniprogram','WeixinServeApiController@miniProgramServe');
$router->get('/serve/foolisholdcodingman','WeixinServeApiController@officialServe');
$router->post('/serve/foolisholdcodingman','WeixinServeApiController@officialServe');

