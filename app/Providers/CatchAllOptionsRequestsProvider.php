<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/28
 * Time: 09:09
 */

namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class CatchAllOptionsRequestsProvider extends ServiceProvider
{
    public function register()
    {
        $request = app('request');

        if ($request->isMethod('OPTIONS'))
        {
            app()->options($request->path(), function() {
                return response('', 200)->header('Access-Control-Allow-Origin', '*');
            });
        }
    }

}