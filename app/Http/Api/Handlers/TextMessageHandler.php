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
        if ($openid=='ofSvBt7vapubGyEEZV9ktIIv__Ik') {
            return "老板你好。";
        }
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