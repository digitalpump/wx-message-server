<?php
/**
 * Created by PhpStorm.
 * User: Jeffrey zuo
 * Mail: zuoyaofei@icloud.com
 * Date: 18/3/7
 * Time: 13:17
 */

namespace App\Console\Commands;


use App\Common\Tools\WxTemplateMessage\ReserveSuccessMessage;
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
        app('JwtUser')->setId(1);

        $touser = "jeffrey";
        $message = new ReserveSuccessMessage($touser,'form_id','index?aa=1');
        $message->addKeyWord("keyword1","中国通知");
        $message->addKeyWord("keyword2","外婆通知");
        $message->addKeywordColor('keyword1',"#FFFFEE");
        $result = $message->spew();
        echo \GuzzleHttp\json_encode($result);
        Event::fire(new RefundEvent(12,-1,0));
    }
}