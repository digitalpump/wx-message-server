<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/22
 * Time: 18:47
 */

namespace App\Events;


class MessageEvent extends Event
{
    public function __construct($message,$appid="")
    {
        $this->message = $message;
        $this->appid = $appid;
    }

}