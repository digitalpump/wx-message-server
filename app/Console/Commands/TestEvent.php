<?php
/**
 * Created by PhpStorm.
 * User: Jeffrey zuo
 * Mail: zuoyaofei@icloud.com
 * Date: 18/3/7
 * Time: 13:17
 */

namespace App\Console\Commands;


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
        Event::fire(new RefundEvent(12,-1,0));
    }
}