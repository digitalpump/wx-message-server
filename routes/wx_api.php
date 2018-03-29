<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:58
 */
$router->get('/service','WeixinApiController@officialServe');
$router->post('/service','WeixinApiController@officialServe');
$router->get('/serve/miniprogram','WeixinApiController@miniProgramServe');
$router->post('/serve/miniprogram','WeixinApiController@miniProgramServe');