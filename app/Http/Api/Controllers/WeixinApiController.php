<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:53
 */

namespace App\Http\Api\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
class WeixinApiController extends Controller
{

    public function serve(Request $request) {

        Log::debug("GET ALL:".json_encode($_GET));
        Log::debug(json_encode($request->all()));

        return response("OOOOK");
       /* $app = app('wechat.official_account');
        $app->server->push(function($message){
            Log::debug("message:".json_encode($message));
            return $message['echostr'];
        });

        return $app->server->serve();*/
    }

}