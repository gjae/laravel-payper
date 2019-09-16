<?php

namespace Gjae\LaravelPayper;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade {

    public function getFacadeAccessor() { return "Payper"; }

}