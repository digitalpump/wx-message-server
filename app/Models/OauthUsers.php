<?php

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/18
 * Time: 16:04
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OauthUsers extends Model
{
    const UPDATED_AT = 'last_login_time';
    protected $table = "crs_oauth_users";
    /*
    from
    open_id
    union_id
    user_id
    last_login_ip
    login_times
    */
    public function users() {
        return $this->belongsTo('App\Models\Users','user_id');
    }
}