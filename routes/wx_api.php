<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:58
 */
$router->get('/service','WeixinServeApiController@testOfficialServe');
$router->post('/service','WeixinServeApiController@testOfficialServe');
$router->get('/serve/miniprogram','WeixinServeApiController@miniProgramServe');
$router->post('/serve/miniprogram','WeixinServeApiController@miniProgramServe');
$router->get('/serve/foolisholdcodingman','WeixinServeApiController@officialServe');
$router->post('/serve/foolisholdcodingman','WeixinServeApiController@officialServe');

