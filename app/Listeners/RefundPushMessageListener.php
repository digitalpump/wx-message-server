<?php
/**
 * Created by PhpStorm.
 * User: Jeffrey zuo
 * Mail: zuoyaofei@icloud.com
 * Date: 18/3/7
 * Time: 13:32
 */

namespace App\Listeners;

use App\Events\RefundEvent;
class RefundPushMessageListener
{
    public function __construct()
    {
    }
    public function handle(RefundEvent $event) {
        $prev_process_status = $event->prev_process_status;
        $id = $event->refundId;
        $cur_process_status = $event->cur_process_status;
        echo "\r\nRefundPushMessageListener refund prev_process_status=" . $prev_process_status
            . " id=" . $id . " cur_process_status=" . $event->cur_process_status . "\r\n" ;
        echo "\r\n user id=" . app('JwtUser')->getId();
    }
}