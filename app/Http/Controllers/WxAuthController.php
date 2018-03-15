<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/13
 * Time: 23:15
 */

namespace App\Http\Controllers;


use App\Common\Tools\HttpStatusCode;
use App\Common\Tools\UserTools;
use Illuminate\Http\Request;
use App\Common\Tools\RedisTools;
use App\Common\Tools\WxBizDataCrypt;
use Log;
class WxAuthController extends Controller
{
    public function decryptUserInfo(Request $request) {
        $uid = app('JwtUser')->getId();

        $encryptedData = $request->get('encryptedData');
        $iv = $request->get('iv');

        if(empty($encryptedData)) return $this->error(HttpStatusCode::BAD_REQUEST,"参数错误！data not found");

        $session_key = RedisTools::getWxSessionKey($uid);

        $appid = config('weixin.appid');
        $pc = new WxBizDataCrypt($appid,$session_key);
        $data = "";
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        Log::debug("errCode=".$errCode);
        if ($errCode !=0 ) {
            return $this->error(HttpStatusCode::UNAUTHORIZED,'Decrypt data failed.' . $errCode);
        }
        $userinfo['nickName'] = $data->nickName;
        $userinfo['avatarUrl'] = $data->avatarUrl;
        UserTools::saveWxUserInfo($uid,$data);
        return $this->success(['info'=>'Success','userinfo'=>$userinfo]);

    }

}