<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/18
 * Time: 16:32
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    //用户类型
    const USER_TYPE_NORMAL = 2;     //普通用户
    const USER_TYPE_VENDOR = 4;     //商家
    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_PROMOTER = 8; //业务推广员


    //用户状态
    const USER_STATUS_NORMAL = 1; //正常
    const USER_STATUS_VENDOR_FORBIDDEN = 4; //商家封锁
    const USER_STATUS_ALL_FORBIDDEN = 0; //平台封锁

    //性别
    const USER_SEX_UNKNOWN = 0;
    const USER_SEX_BOY = 1;
    const USER_SEX_GIRL = 2;

    protected $table = "crs_users";
    /*
        third_party_name  default ""
        avater      defualt ""
        user_type   default 2
        user_status default 1
        nice_name   default "";
        work_unit_id default 0
        signature   default null
        sex defualt 0
        birthday  defautl null;
    */

    public function oauths() {
        return $this->hasMany('App\Models\OauthUsers','user_id');
    }
}