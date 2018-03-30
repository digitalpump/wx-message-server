<?php

namespace App\Providers;

use App\Common\Tools\Configure\JwtConfigure;
use App\Common\Tools\Configure\WeixinConfigure;
use App\Common\Tools\Jwt\JwtUser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //保存和获取用户信息的单例
       /* $this->app->singleton("JwtUser",function($app) {
            return new JwtUser();
        });
        $this->app->singleton("JwtConfig",function($app) {
           return new JwtConfigure();
        });*/

    }
}
