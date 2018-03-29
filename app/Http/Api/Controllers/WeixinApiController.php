<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:53
 */

namespace App\Http\Api\Controllers;


use App\Http\Api\Handlers\EventMessageHandler;
use App\Http\Api\Handlers\OtherMessageHandler;
use App\Http\Api\Handlers\TextMessageHandler;
use App\Http\Controllers\Controller;
use EasyWeChat\Kernel\Messages\Message;
use Illuminate\Http\Request;
use Log;
class WeixinApiController extends Controller
{

    public function serve(Request $request) {

        $app = app('wechat.official_account');
        $app->server->push(TextMessageHandler::class,Message::TEXT);
        $app->server->push(EventMessageHandler::class,Message::EVENT);
        $app->server->push(OtherMessageHandler::class,Message::ALL);
       /* $app->server->push(function($message){
            Log::debug("message:".json_encode($message));
            switch ($message['MsgType']) {
                case 'event':
                    //return '收到事件消息';
                    break;
                case 'text':
                    //return '收到文字消息';
                    break;
                case 'image':
                    //return '收到图片消息';
                    break;
                case 'voice':
                    //return '收到语音消息';
                    break;
                case 'video':
                    //return '收到视频消息';
                    break;
                case 'location':
                    //return '收到坐标消息';
                    break;
                case 'link':
                    //return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;

            }
            return "Success";
        });*/

        return $app->server->serve();
    }

}