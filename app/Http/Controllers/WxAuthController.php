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
use App\Models\OauthUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        $session_key = UserTools::getWxMiniProgramSessionKeyById($uid);


        if (empty($session_key)) {
            return $this->error(HttpStatusCode::NOT_FOUND,"Session key not found");
        }

        $appid = app('WxConfig')->getMiniProgramAppId();
        if(empty($appid)) {
            return $this->error(HttpStatusCode::NO_CONTENT,"Weixin appid not found in configure");
        }
        $pc = new WxBizDataCrypt($appid,$session_key);
        $data = "";
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode !=0 ) {
            return $this->error(HttpStatusCode::UNAUTHORIZED,'Decrypt data failed.' . $errCode);
        }
        $userinfo['nickName'] = $data->nickName;
        $userinfo['avatarUrl'] = $data->avatarUrl;
        UserTools::updateUserWithInfoFromWx($uid,$data);
        return $this->success(['info'=>'Success','userinfo'=>$userinfo]);

    }

}