<?php

namespace App\Http\FirstVersion\Controllers;

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
        return $this->success(['uid'=>$uid,'info'=>'hello lumen world']);
    }

    //
}
