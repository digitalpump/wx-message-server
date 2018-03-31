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
use Symfony\Component\Debug\Exception\FatalThrowableError;

class WeixinApiController extends Controller
{

    /**
     * 愚公码头公众号服务接口
     * @param Request $request
     */
    public function officialServe(Request $request) {

        $config = [
            'token'          => env('WECHAT_FCM_OFFICIAL_ACCOUNT_TOKEN'),
            'appid'          => env('WECHAT_FCM_OFFICIAL_ACCOUNT_APPID'),
            'appsecret'      => env('WECHAT_FCM_OFFICIAL_ACCOUNT_SECRET'),
            'encodingaeskey' => env('WECHAT_FCM_OFFICIAL_ACCOUNT_AES_KEY'),
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

    /**
     * 测试公众号服务接口
     * @param Request $request
     */
    public function testOfficialServe(Request $request) {

        $config = [
            'token'          => env('WECHAT_TEST_OFFICIAL_ACCOUNT_TOKEN'),
            'appid'          => env('WECHAT_TEST_OFFICIAL_ACCOUNT_APPID'),
            'appsecret'      => env('WECHAT_TEST_OFFICIAL_ACCOUNT_SECRET'),
            'encodingaeskey' => env('WECHAT_TEST_OFFICIAL_ACCOUNT_AES_KEY'),
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

    /**
     * 小程序服务接口
     * @param Request $request
     */
    public function miniProgramServe(Request $request) {
        $config = [
            'token'          => env('WECHAT_MINI_PROGRAM_TOKEN'),
            'appid'          => env('WECHAT_MINI_PROGRAM_APPID'),
            'appsecret'      => env('WECHAT_MINI_PROGRAM_SECRET'),
            'encodingaeskey' => env('WECHAT_MINI_PROGRAM_AES_KEY'),
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

    private function settingBizProcess($config) {
        //$receier = new \WeChat\Receive($config);
        //$receier->reply()
    }
    private function serviceBizProcess($config) {

        try {
            $receier = new \WeChat\Receive($config);
        } catch (FatalThrowableError $exception) {
            Log::error($exception->getMessage());
            return "服务器身体不适，请稍后再试。";
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "服务器罢工了，请稍后再试。";
        }

        $message = $receier->getReceive();

        $msgType = $receier->getMsgType();

        try {
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
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "服务器宝宝病了，请稍后再试。";
        }

    }

}