<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:53
 */

namespace App\Http\Api\Controllers;


use Illuminate\Http\Request;
use Log;
class WeixinApiController
{

    public function service(Request $request) {

        Log::debug("GET ALL:".json_encode($_GET));
        Log::debug(json_encode($request->all()));

        $app = app('wechat.official_account');
        $app->server->push(function($message){
            Log::debug("message:".json_encode($message));
            return $message['echostr'];
        });

        return $app->server->serve();
    }

}