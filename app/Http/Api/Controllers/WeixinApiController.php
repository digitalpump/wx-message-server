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

    public function officialServe(Request $request) {

        $config = [
            'token'          => 'EqfMeTHlYi817bol9t9uZ778JzoG0Kvm',
            'appid'          => 'wxc496505548ed228f',
            'appsecret'      => 'c3d953d56cf61b8578c73b894468c18a',
            'encodingaeskey' => '',
            // 配置商户支付参数（可选，在使用支付功能时需要）
            //'mch_id'         => "1235704602",
            //'mch_key'        => 'IKI4kpHjU94ji3oqre5zYaQMwLHuZPmj',
            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
            //'ssl_key'        => '',
            //'ssl_cer'        => '',
            // 缓存目录配置（可选，需拥有读写权限）
            //'cache_path'     => '',
        ];
        $receier = new \WeChat\Receive($config);
        $message = $receier->getReceive();
        $msgType = $receier->getMsgType();

        switch ($msgType) {
            case 'event':
                //return '收到事件消息';
                $receier->text("收到事件消息")->reply();
                break;
            case 'text':
                $handler = new TextMessageHandler();
                //return $handler->handle($message);
                $receier->text($handler->handle($message))->reply();
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
                $receier->text("收到其它消息")->reply();
                //return '收到其它消息';
                break;
        }
        /*
        $app = app('wechat.official_account');
        //$app->server->push(TextMessageHandler::class,Message::TEXT);
        //$app->server->push(EventMessageHandler::class,Message::EVENT);
        //$app->server->push(OtherMessageHandler::class,Message::ALL);
       $app->server->push(function($message){
            Log::debug("message:".json_encode($message));
            switch ($message['MsgType']) {
                case 'event':
                    //return '收到事件消息';
                    break;
                case 'text':
                    $handler = new TextMessageHandler();
                    return $handler->handle($message);
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
        });

        return $app->server->serve();
        */
    }

    public function miniProgramServe(Request $request) {
        $app = app('wechat.mini_program');
        $app->server->push(OtherMessageHandler::class,Message::ALL);
        return $app->server->serve();
    }

}