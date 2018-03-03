<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/3
 * Time: 23:30
 */

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Configure logging on boot.
     *
     * @return void
     */
    public function boot()
    {
        $maxFiles = 0;
        $filepath = '/logs/'.date('Y-m').'/api.log';
        $format = "[%datetime%] %level_name%: %message% %context% %extra%\n";
        $handlers[] = (new RotatingFileHandler(storage_path($filepath), $maxFiles))
            ->setFormatter(new LineFormatter($format, null, true, true));
        $this->app['log']->setHandlers($handlers);
    }

    /**
     * Register the log service.
     *
     * @return void
     */
    public function register()
    {
        // Log binding already registered in vendor/laravel/lumen-framework/src/Application.php.
    }

}