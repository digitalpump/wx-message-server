<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 22:37
 */

namespace App\Http\Api\Handlers;


use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use Log;
class EventMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        // TODO: Implement handle() method.
        Log::debug("@EventMessageHandler payload=".json_encode($payload));
        return true;
    }


}