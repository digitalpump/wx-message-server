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
        $this->serviceBizProcess($config);
    }

    public function miniProgramServe(Request $request) {
        $config = [
            'token'          => 'wJ5UKoj6Z99jGcs1VqE9bUQN54nXlb25',
            'appid'          => 'wxdfd474a204719893',
            'appsecret'      => '30270ce9f2b7463e4720995cdc220c6c',
            'encodingaeskey' => 'sR2iEGvc2MHo8AEwn1GSj7W3g3HAwikNqjmkENc6bzz',
            // 配置商户支付参数（可选，在使用支付功能时需要）
            //'mch_id'         => "1235704602",
            //'mch_key'        => 'IKI4kpHjU94ji3oqre5zYaQMwLHuZPmj',
            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
            //'ssl_key'        => '',
            //'ssl_cer'        => '',
            // 缓存目录配置（可选，需拥有读写权限）
            //'cache_path'     => '',
        ];

        $this->serviceBizProcess($config);
    }

    private function serviceBizProcess($config) {
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
    }

}