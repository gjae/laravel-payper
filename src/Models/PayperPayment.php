<?php
namespace Gjae\LaravelPayper\Models;
use Illuminate\Database\Eloquent\Model;

use Gjae\LaravelPayper\Contracts\PaymentContract;
class PayperPayment extends Model  implements PaymentContract
{

    protected $table = 'payper_payments';
    protected $fillable = [
        'auth_guid', 'auth_resp', 'batch_id', 'response_description', 'tran_nbr', 'reference', 'amount', 'description', 'extra_data', 'response_code'
    ];

    public function models()
    {
        return $this->hasMany(\Gjae\LaravelPayper\Models\ModelHasTransaction::class, 'payper_payment_id');
    }

    public function setMetodousadoAttribute($old)
    {
        $this->attributes['metodousado'] = ( strtolower($old) == 'westerunion' ) ? 'WU' : $old;
    }

    public function getExtraDataAttribute($old)
    {
        return json_decode($old);
    }

}