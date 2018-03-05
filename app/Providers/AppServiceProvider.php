<?php

namespace App\Providers;

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
        $this->app->singleton("user",function($app) {

        });
    }
}
