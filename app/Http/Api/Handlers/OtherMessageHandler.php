<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 22:39
 */

namespace App\Http\Api\Handlers;


use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use Log;
class OtherMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        Log::debug("@OtherMessageHandler");

    }


}