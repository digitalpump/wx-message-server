<?php

namespace App\Http\Controllers;

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
        return $this->success(["your"=>"jeffrey"]);
    }

    public function getout(Request $request) {
        return $this->error();
    }

    //
}
