<?php

namespace Gjae\LaravelPayper;

trait Payable {

    public function payments(){
        return $this->morphMany('Gjae\LaravelPayper\Models\PayperPayment', 'payable');
    }

}