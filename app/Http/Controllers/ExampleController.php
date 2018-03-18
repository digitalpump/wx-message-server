<?php

namespace App\Http\Controllers;

use App\Models\OauthUsers;
use App\Models\Users;
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
        $user = app('JwtUser')->getUser();
        return $this->success(['user'=>$user,'info'=>'hello lumen world']);
    }

    //
}
