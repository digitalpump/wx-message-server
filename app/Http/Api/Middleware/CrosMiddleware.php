<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/28
 * Time: 08:52
 */

namespace App\Http\Api\Middleware;


class CrosMiddleware
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE');
        $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
        $response->header('Access-Control-Allow-Origin', '*');
        return $response;
    }
}