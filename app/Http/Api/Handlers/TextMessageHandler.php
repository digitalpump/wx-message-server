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
       //MsgId
        $msgId = $payload['MsgId'];
        $openid = $payload['FromUserName'];
        $content = $payload['Content'];
        if (trim($content)=="1095592") {
            return "OK.".$openid;
        }
       //Log::debug("@TextMessageHandler from user:".$payload['FromUserName']);
       //Log::debug("@TextMessageHandler Content:".$payload['Content']);
    }

}