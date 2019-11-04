<?php

namespace Gjae\LaravelPayper\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ModelHasTransaction extends Model
{
    use SoftDeletes;
    protected $table = 'model_has_transactions';

    protected $fillable = [
        'model_id', 'model_type', 'payper_payment_id'
    ];

    public function transaction()
    {
        return $this->belongsTo(\Gjae\LaravelPayper\Models\PayperPayment::class, 'payper_payment_id');
    }

    public function model()
    {
        return $this->morphTo();
    }
}