<?php
/**
 * Created by PhpStorm.
 * User: Jeffrey zuo
 * Mail: zuoyaofei@icloud.com
 * Date: 18/3/7
 * Time: 13:17
 */

namespace App\Console\Commands;


use App\Common\Tools\MessageTools;
use App\Common\Tools\WxTemplateMessage\ReserveSuccessMessage;
use App\Common\Tools\WxTemplateMessage\TestMessage;
use App\Events\MessageEvent;
use App\Events\RefundEvent;
use Illuminate\Console\Command;
use Event;

class TestEvent extends Command
{
    protected $name = 'cs:testEvent';

    protected $signature = 'cs:testEvent';

    protected $description = 'Test fire event.';

    public function __construct() {
        parent::__construct();
    }

    public function handle()
    {

        $touser = "oHNyO4niJX0eALpAu-0al3I8LVcE";

        $messageTools = new MessageTools("");
        $messageTools->sendMessage(function() use($touser) {
            $message = new ReserveSuccessMessage($touser,'form_id','index?aa=1');
            $message->addKeyWord("keyword1","中国通知商品");
            $message->addKeyWord("keyword2","外婆通知商家");
            $message->addKeyWord("keyword3","外婆通知门店");
            $message->addKeyWord("keyword4","XXXYYYYKKCKCKKD");
            $message->addKeyWord("keyword5","2017-12-1 12:00:00");
            $message->addKeyWord("keyword6","2017-12-1 14:00:00");
            $message->addKeyWord("keyword7","2 hours");
            $message->addKeyWord("keyword8","别看广告");
            $message->addKeywordColor('keyword1',"#FFFFEE");
            return $message;
          }
        );

        //Event::fire(new MessageEvent(\GuzzleHttp\json_encode($result)));

        $touser2 = "ofSvBt7vapubGyEEZV9ktIIv__Ik";

        $appid = "wxc496505548ed228f";

        $messageTools2 = new MessageTools($appid);
        $messageTools2->sendMessage(function() use ($touser2) {
            $object =  new TestMessage($touser2);
            $object->addKeyWord("keyword1","中国通知商品");
            $object->addKeyWord("keyword2","外婆通知商家");
            $object->setJumpUrl("http://hs.ontheroadstore.com/Portal/Index/index.html");
            return $object;
        });
        //Event::fire(new MessageEvent(\GuzzleHttp\json_encode($result2),$appid));
    }
}