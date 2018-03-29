<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 22:28
 */

namespace App\Http\Api\Handlers;


use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use Log;
class TextMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        Log::debug("@TextMessageHandler payload=".json_encode($payload));
       //MsgId
        $msgId = $payload['MsgId'];
        $openid = $payload['FromUserName'];
        $content = $payload['Content'];

        //帮助，命令行列表
        //TODO 简单独立的命令优先级最高，比如查我的openid
        if (trim($content)=="我是谁") {
            return $openid;
        }
        if ($openid=='ofSvBt7vapubGyEEZV9ktIIv__Ik') {

            //TODO boss command.
            return "老板你好。";
        }

        //TODO 记录用户command  和 组合command的状态机
        /*
         * 比如用户开始注册公众号
         *
         * 1 步骤      状态机 process_status 0 等待
         */

        if (trim($content)=="我要上天") {
            //TODO 查用户有没有注册
            //有注册
            //无注册，发送注册验证码
            return "OK.".$openid;
        }
       //Log::debug("@TextMessageHandler from user:".$payload['FromUserName']);
       //Log::debug("@TextMessageHandler Content:".$payload['Content']);
    }



}