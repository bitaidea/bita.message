<?php

namespace Bita\Message;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    public function register()
    {
        App::bind('message', function () {
            return new SmsAdapter;
        });
    }
}
