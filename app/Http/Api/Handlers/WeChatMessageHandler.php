<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/30
 * Time: 15:54
 */

namespace app\Http\Api\Handlers;


interface WeChatMessageHandler
{
    public function handle($payload);
}