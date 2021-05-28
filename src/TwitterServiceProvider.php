<?php

namespace AngryMoustache\Twitter;

use Illuminate\Support\ServiceProvider;

class TwitterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('twitter', Twitter::class);
    }
}
