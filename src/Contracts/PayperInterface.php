<?php

namespace Gjae\LaravelPayper\Contracts;

interface PayperInterface {

    public function getPayperMD5Key()   : string;
    public function getPayperUser()     : string;
    public function getPayperCurrency() : string;
    public function getPayperTax()      : float;
    public function getPayperURLCheckout() : string;
    public function getPayperURLBack()  : string; 

    public function getUniqueReference() : string;

}