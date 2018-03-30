<?php

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/18
 * Time: 16:04
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OauthUser extends Model
{
    const FROM_WX_MINI_PROGRAM = "miniprogram";
    const FROM_WX_APP = "wxapp";
    const FROM_WX_WEB = "wxweb";

    //const UPDATED_AT = 'last_login_time';
    protected $table = "oauth_user";
    /*
    from
    open_id
    union_id
    user_id
    last_login_ip
    login_times
    */
    public function user() {
        return $this->belongsTo('App\Models\User','user_id');
    }
}