<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/30
 * Time: 12:29
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BizOrder extends Model
{
    protected $table = "biz_order";
    public function user() {
        return $this->belongsTo('App\Models\User','user_id');
    }

}