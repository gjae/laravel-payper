<?php
namespace Gjae\LaravelPayper\Models;
use Illuminate\Database\Eloquent\Model;

class PayperPayment extends Model {

    protected $table = 'payper_payments';

    public function payable(){
        return $this->morphTo();
    }

}