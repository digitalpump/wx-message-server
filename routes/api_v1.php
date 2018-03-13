<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$app->group(['prefix' => 'appv5', 'namespace' => 'App\Http\Controllers', 'middleware' => ['auth', 'active_status','after']], function () use ($app) {

//}
$router->get('/welcome','ExampleController@welcome');
$router->get('/getout','ExampleController@getout');
$router->post('/login/email','JWTAuthController@emailLogin');
$router->post('/login/weixin','JWTAuthController@wxLogin');
$router->post('/token/refresh','JWTAuthController@refreshToken');