<?php
namespace Gjae\LaravelPayper\Models;
use Illuminate\Database\Eloquent\Model;

class PayperPayment extends Model {

    protected $table = 'payper_payments';
    protected $fillable = [
        'referencia', 'moneda', 'respuesta', 'cuentanro', 'metodousado', 'autorizacion', 'nrotransaccion', 'payable_type','payable_id', 'valor'
    ];

    public function payable(){
        return $this->morphTo();
    }

    public function setMetodousadoAttribute($old)
    {
        $this->attributes['metodousado'] = ( strtolower($old) == 'westerunion' ) ? 'WU' : $old;
    }

}