<?php

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 07:16
 */
$router->post('/message/send','MessageApiController@sendMessage');
$router->get('/get/accesstoken','MessageApiController@getAccessToken');
$router->get('/hello','ExampleController@hello');

