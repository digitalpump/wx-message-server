<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/30
 * Time: 17:14
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class VendorWxAccount extends Model
{
    protected $table = "vendor_wx_account";

    public function user() {
        return $this->belongsTo('App\Models\User','vendor_id');
    }
}