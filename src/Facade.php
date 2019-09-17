<?php

namespace Gjae\LaravelPayper;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade {

    public static function getFacadeAccessor() { return "Payper"; }


    public static function __callStatic($method, $args)
    {
        $instance = static::$app->make( static::getFacadeAccessor() );
        return \call_user_func_array([ $instance, $method ], $args);
    }
}