<?php

namespace Bita\Message;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/bitamessage.php' => config_path('bitamessage.php'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }

    public function register()
    {
        App::bind('message', function () {
            return new SmsAdapter;
        });
    }
}
