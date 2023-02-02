<?php

namespace Emotality\LaravelColor;

use Illuminate\Support\Facades\Facade;

class Color extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Emotality\LaravelColor\LaravelColor::class;
    }
}
