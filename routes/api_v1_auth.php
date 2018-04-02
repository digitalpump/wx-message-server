<?php

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 07:16
 */
$router->post('/template/message/send','WxMessageApiController@sendTemplateMessage');
$router->post('/customer/message/send','WxMessageApiController@sendCustomMessage');
$router->get('/get/accesstoken','WxMessageApiController@getAccessToken');

$router->post('/customer/add','WxCustomerApiController@addCustomer');
$router->post('/customer/update','WxCustomerApiController@updateCustomer');
$router->post('/customer/delete','WxCustomerApiController@delCustomer');
