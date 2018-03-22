<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/22
 * Time: 18:44
 */

namespace App\Common\Tools;

use App\Events\MessageEvent;
use Event;
class MessageTools
{
    private $appid = "";
    public function __construct($appid="")
    {
        $this->appid = $appid;
    }

    public function sendMessage(callable $callback) {
        $object = call_user_func($callback);
        if(empty($object)) return;
        Event::fire(new MessageEvent(json_encode($object->spew()),$this->appid));
    }
}