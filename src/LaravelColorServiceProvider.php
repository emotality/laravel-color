<?php

namespace Emotality\LaravelColor;

use Illuminate\Support\ServiceProvider;

class LaravelColorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // TODO: Add config to set:
        // const BRIGHTNESS_PERCENT = 50;
        // const FONT_LIGHT = '#ffffff';
        // const FONT_DARK = '#000000';
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        //
    }
}
