<?php

namespace App\Http\FirstVersion\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    /**
     * @param array|object $data
     * @param int $code
     * @param string $info
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data,$info="Success",$code=1) {
        return $this->response_json($info,$data,$code);
    }

    protected function error($info="Failed",$data=null,$code=0) {
       return $this->response_json($info,$data,$code);
    }

    private function response_json($info,$data,$code) {
        if(empty($data)) {
            return response()->json(['code'=>$code,'message'=>$info,'data'=>new \stdClass()]);
        }
        return response()->json(['code'=>$code,'message'=>$info,'data'=>$data]);
    }

}
