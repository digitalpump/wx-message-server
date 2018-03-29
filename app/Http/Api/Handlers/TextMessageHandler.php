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
       Log::debug("from user:".$payload->FromUserName);
       Log::debug("Content:".$payload->Content);
    }

}