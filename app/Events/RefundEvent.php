<?php
/**
 * Created by PhpStorm.
 * User: Jeffrey zuo
 * Mail: zuoyaofei@icloud.com
 * Date: 18/3/7
 * Time: 11:08
 */

namespace App\Events;


class RefundEvent extends Event
{
    public function __construct($refundId,$prev_process_status,$cur_process_status)
    {
        $this->refundId = $refundId;
        $this->cur_process_status = $cur_process_status;
        $this->prev_process_status = $prev_process_status;
    }
}