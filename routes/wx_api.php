<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:58
 */
$router->get('/service','MessageApiController@wxServe');
$router->post('/service','MessageApiController@wxServe');
