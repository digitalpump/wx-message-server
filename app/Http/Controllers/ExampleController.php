<?php

namespace App\Http\Controllers;

use App\Models\OauthUser;
use App\Models\User;
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
        $id = app('JwtUser')->getId();
        $oauth = User::find($id)->oauths()->where('from',OauthUser::FROM_WX_MINI_PROGRAM)->first();
        return $this->success([['user'=>$user,'oauth'=>$oauth],'info'=>'hello lumen world']);
    }

    //
}
