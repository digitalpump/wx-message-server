<?php

namespace App\Http\Controllers;

use App\Common\Tools\RedisTools;
use App\Common\Tools\WxBizDataCrypt;
use Illuminate\Http\Request;
use Log;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function welcome(Request $request) {
        Log::info("I am jeffrey");
        $log_channel = config('app.log_channel');
        return $this->success(["your"=>"jeffrey",'channel'=>$log_channel]);
    }

    public function getout(Request $request) {
        return $this->error();
    }

    public function hello(Request $request) {
        $uid = app('JwtUser')->getId();
        $session_key = RedisTools::getWxSessionKey($uid);

        $appid = config('weixin.appid');
        $pc = new WxBizDataCrypt($appid,$session_key);
        $encryptedData="iFnf1yogFn+CSv5pWrr4gKCdr8WLn2eNiNOpUDu7uF5qKLdUBb7WuqfHNZG7sJDar82t3G0b9iBab8nbjm8F1XkyTloKWA2irRIllozK4L80p3Awo1r9UJp1M78/7cbrtXwPKsnjU5OBct+h44COjawhi+NyiwzznmbkojCwCbAEbDUngWoc+5vbnDW+7cwNJPgdj+s+JpE8mP2uwOZCEj6TWyDSH4W8elKTHFH4Aiagt8rsxZYCGsrnxXNjBtb2p8wCCb7a/z8rDWTcM2/HOTFDT4/hrlPNPqao8QgzT8GKX55ZVucaw03rOj5RPoRMnE6eKxbMOvq8BEHfyNp0NFYF9+6D9eHIEWR/t2ok/V7lMD7iWKnDM1x6JaZG5qwFSSIXf7una9QBdu960mNxKYXeQ50KC4iMwzwgp3JhqU0HfSKE29EqmmutrR9ffQByksuEDSwvTJwT6HDgJLqERA==";


        $iv = "12gafT5uL2vjzweUtOCZMA==";

        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        Log::debug("errCode=".$errCode);
        return $this->success(['uid'=>$uid,'info'=>'hello lumen world','data'=>$data]);
    }

    //
}
