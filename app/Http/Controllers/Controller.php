<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    /**
     * @param array|object $data
     * @param int $code
     * @param string $info
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data,$code=1,$info="Success") {
        return response()->json(['code'=>$code,'info'=>$info,'data'=>$data]);
    }

}
