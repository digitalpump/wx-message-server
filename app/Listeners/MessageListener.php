<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/22
 * Time: 18:46
 */

namespace App\Listeners;


use App\Events\MessageEvent;
use Illuminate\Support\Facades\Redis;
use Log;
class MessageListener
{

    public function handle(MessageEvent $event) {
        if(empty($event)) return;
        if(empty($event->message)) return;
        $appid = "";
        if(empty($event->appid)) {
            $appid = app('WxConfig')->getMiniProgramAppId();
        } else {
            $appid = $event->appid;
        }

        $key = env('MESSAGE_POOL_KEY_RPEFIX') . $appid;
        $ret = Redis::rpush($key,$event->message);
        if(empty($ret)) {
            Log::error("Push message to redis error:" . $key . " , value=" . $event->message);
        }

    }
}