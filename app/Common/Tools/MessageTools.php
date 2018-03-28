<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/22
 * Time: 18:44
 */

namespace App\Common\Tools;

class MessageTools
{
    private $appid = "";
    public function __construct($appid="")
    {
        $this->appid = $appid;
    }

    public function sendMessage($msgObject,callable $sendTools) {
        if(empty($msgObject)) return false;
        if(!is_object($msgObject)) return false;
        try {
            $body = $msgObject->spew();
        } catch (\Exception $e) {
            return false;
        }
        if(empty($body)) return false;
        //方便使用其它各种消息发送方式，如redis ,http 等
        return call_user_func($sendTools,json_encode($body),$this->appid);
    }
}