<?php

namespace App\Http\Controllers;

use App\Common\Tools\HttpStatusCode;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    /**
     * @param array|object $data
     * @param int $code
     * @param string $info
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data,$info="Success",$code=HttpStatusCode::OK) {
        return $this->response_json($info,$data,$code);
    }

    protected function error($code=HttpStatusCode::BAD_REQUEST,$info="Failed",$data=null) {
       return $this->response_json($info,$data,$code);
    }

    private function response_json($info,$data,$code) {
        if(empty($data)) {
            return response()->json(['message'=>$info,'data'=>new \stdClass()],$code);
        }
        return response()->json(['message'=>$info,'data'=>$data],$code);
    }

}
