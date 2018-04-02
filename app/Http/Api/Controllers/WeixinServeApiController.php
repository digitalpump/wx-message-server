<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 15:53
 */

namespace App\Http\Api\Controllers;


use App\Common\Tools\HttpStatusCode;
use App\Http\Api\Handlers\TextMessageHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Support\Facades\Redis;
class WeixinServeApiController extends Controller
{

    public function monitor(Request $request) {
        $redis_key = "monitor_redis_test_key";

        //TODO test redis set and read

        try {
            $result = Redis::setex($redis_key,3,"Good job!");
        } catch (\Exception $exception) {
            return $this->error(HttpStatusCode::INTERNAL_SERVER_ERROR,$exception->getMessage());
        }

        if(empty($result)) return $this->error(HttpStatusCode::NOT_ACCEPTABLE,"Redis write error");

        try {
            $result = Redis::get($redis_key);

        } catch (\Exception $exception) {
            return $this->error(HttpStatusCode::INTERNAL_SERVER_ERROR,$exception->getMessage());
        }
        if(empty($result)) return $this->error(HttpStatusCode::NOT_ACCEPTABLE,"Redis read error");
        return $this->success($result);
    }
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
     *
     * 测试公众号2服务接口 185号
     * @param Request $request
     */
    public function test2OfficialServe(Request $request) {

        $config = [
            'token'          => env('WECHAT_TEST2_OFFICIAL_ACCOUNT_TOKEN'),
            'appid'          => env('WECHAT_TEST2_OFFICIAL_ACCOUNT_APPID'),
            'appsecret'      => env('WECHAT_TEST2_OFFICIAL_ACCOUNT_SECRET'),
            'encodingaeskey' => env('WECHAT_TEST2_OFFICIAL_ACCOUNT_AES_KEY'),
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
        Log::info("message:".json_encode($message));
        $msgType = $receier->getMsgType();
        $defualtResponse = "命令参考：我是谁,什么情况,其它密秘指令不告诉你：）";
        try {
            switch ($msgType) {
                case 'event':
                    //return '收到事件消息';
                    if ($message['Event']=="subscribe") {
                        //记录关注时间
                        $receier->text("你终于来了，服务器都等超时了")->reply();
                    } else if($message['Event']=="unsubscribe") {
                        //记录取消息关注时间--->写入取关用户记录表，uid,openid,加入时间，取关时间，总计停留时长（多少分钟）
                        //TODO 用户取关操作
                    } else {
                        $receier->text("收到事件消息")->reply();
                    }

                    break;
                case 'text':
                    $handler = new TextMessageHandler();
                    //return $handler->handle($message);
                    $receier->text($handler->handle($message))->reply();
                    //return '收到文字消息';
                    break;
                case 'image':
                    //return '收到图片消息';
                    $receier->text($defualtResponse)->reply();
                    break;
                case 'voice':
                    //return '收到语音消息';
                    $receier->text($defualtResponse)->reply();
                    break;
                case 'video':
                    //return '收到视频消息';
                    $receier->text($defualtResponse)->reply();
                    break;
                case 'location':
                    //return '收到坐标消息';
                    $receier->text($defualtResponse)->reply();
                    break;
                case 'link':
                    //return '收到链接消息';
                    $receier->text($defualtResponse)->reply();
                    break;
                // ... 其它消息
                default:
                    $receier->text("你要开心哦")->reply();
                    //return '收到其它消息';
                    break;
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "服务器宝宝病了，请稍后再试。";
        }

    }

}