<?php 

namespace Gjae\LaravelPayper;

use Gjae\LaravelPayper\Contracts\IPayer;
use Gjae\LaravelPayper\Contracts\HasTransaction;
use Gjae\LaravelPayper\Models\PayperPayment;
use Gjae\LaravelPayper\Models\ModelHasTransaction;

// Exceptions

use Gjae\LaravelPayper\Exceptions\PayperConfigException as ConfigException;
use Gjae\LaravelPayper\Exceptions\PayableNullException;
use Gjae\LaravelPayper\Exceptions\NoHasTransactionImplementException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;

use Gjae\LaravelPayper\Contracts\PaymentContract;

use DB;
use Closure;
class Payper implements PaymentContract
{

    /**
     * La configuracion global de la libreria
     * especificada el el archivo config/payper.php
     *
     * @var array
     */
    private $config     = null;
    

    /**
     * Los datos de la solicitud HTTP
     * util para capturar los datos que seran necesarios para guardar
     * los datos de la transaccion devueltos por payper
     *
     * @var Illuminate\Http\Request
     */
    private $request     = null;

    /**
     * UUid de la referencia que sera usada para el pago
     *
     * @var string
     */
    private $reference  = null;


    /**
     * Settea parametros y datos adicionales que deseen ser enviados a payper
     * (estos parametros se retornan a la urlback por get)
     *
     * @var array
     */
    private $aditionalParams = [];


    /**
     * Settea la descripción de la transacción
     *
     * @var string
     */
    private $description = null;


    /**
     * Valor de la transaccion (total)
     *
     * @var float
     */
    private $valor      = 0.00;

    private $transaction = null;


    public function __construct(array $config = null,  Request $request ){
        if( is_null($config) )
            throw new ConfigException("No config available");

        $this->config        = $config;
        $this->request       = $request;
        $this->reference     = $this->getUniqueReference();

    }

    public function getAccessToken()
    {
        return $this->config['access_token'];
    }

    private function checkExcludeCards( $card )
    {
        return in_array( $card , $this->config['exclude_cards'] );
    }
    /**
     * Porcentaje de impuesto especificado en el archivo de configuracion
     *
     * @return float
     */
    public function getPayperTax() : float
    {
        $value = $this->checkNullValue('porcentaje_impuesto', 'porcentaje de impuesto / tax percent');
        return floatval($value);
    }

    /**
     * Settea el valor de impuesto para personalizarlo de acuerdo a su uso
     *
     * @param float $tax
     * @return void
     */
    public function setPayperTax(float $tax = 0.00)
    {
        $this->config['porcentaje_impuesto']    = $tax;
    }

    /**
     * URL del checkout especificado en el archivo
     * de configuracion
     *
     * @return string
     */
    public function getPayperURLCheckout() : string
    {
        return $this->checkNullValue('checkout_url', 'checkout url');
        
    }

    /**
     * Settea el valor que sera usado en 
     * la configuracion del formulario que sera enviado a payper
     *
     * @param float $newValor
     * @return void
     */
    public function setValor($newValor)
    {
        $this->valor = $newValor;
    }

    /**
     * Obtiene el valor setteado en el metodo anterior
     *
     * @return void
     */
    public function getValor()
    {
        return $this->valor;
    }

    public function setReferencia(string $newReferencia)
    {
        $this->reference = $newReferencia;
    }

    public function setAditionalData(array $data)
    {
        $this->aditionalParams = $data;
    }

    public function getAditionalData() : array
    {
        return $this->aditionalParams;
    }

    public function getAditionalDataAsQueryString() : ?string 
    {
        if( count( $this->getAditionalData() ) > 0)
        {
            return \http_build_query( $this->getAditionalData() );
        }

        return null;
    }

    public function getAditionalDataAsJson()
    {
        return json_encode( count($this->getAditionalData()) > 0 ? $this->getAditionalData() : [] );
    }

    public function setDescription(string $description = "")
    {
        $this->description = $description;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }
    /**
     * Inicia la transaccion pasand como argumento al closure
     * la instancia para asi settear los parametros adicionales
     *
     * @param Closure $function
     * @return void
     */
    public function begin(Closure $closure, $withModel = null)
    {
        DB::beginTransaction();
        try
        {
            $closure($this);
            $this->makeTransaction();
            $this->modelHasTransaction($withModel);
            DB::commit();
            return $this;
        }catch(\Exception $e)
        {
            DB::rollback();
            return $e;
        }

        return $this;
    }

    private function modelHasTransaction( $withModel = null )
    {
        if( !is_null($withModel) )
        {
            if( is_array( $withModel ) )
            {
                foreach( $withModel as $model ) $this->saveModel( $model );
            }
            else if( is_object( $withModel ) && ( $withModel instanceof HasTransaction ) )
                $this->saveModel( $withModel );
            else if( is_object( $withModel ) && !( $withModel instanceof HasTransaction ) )
                $this->throwHasModelException();
        }
    }

    private function saveModel(HasTransaction $model)
    {
        $model->transactions()->save(
            new ModelHasTransaction([
                'payper_payment_id' => $this->transaction->id
            ])
        );
    }

    private function makeTransaction()
    {
        $this->transaction = PayperPayment::create([
            'amount'    => $this->getTotal(),
            'reference' => $this->getReference(),
            'description' => $this->getDescription(),
            'extra_data'  => $this->getAditionalDataAsJson()
        ]);
    }

    private function throwHasModelException()
    {
        throw new NoHasTransactionImplementException('Object need implements Gjae\\LaravelPayper\\Contracts\\HasTransaction contract');
    }

    /**
     * retorna el UUid de la referencia de la transaccion
     *
     * @return string
     */
    public function getReference() : string
    {
        return $this->reference;
    }

    public function getTotal()
    {
        $tax_percent    = $this->getPayperTax();
        $total          = 0.00;
        $total  = $this->getValor() + ( ( $this->getValor() * $tax_percent ) / 100 );

        return $total;
    }

    public function getTotalTax()
    {
        $tax_percent    = $this->getPayperTax();

        return ($this->getValor() * $tax_percent) / 100;
    }

    public function request()
    {
        return $this->request;
    }

    public function save(IPayable $payable = null)
    {
        if( is_null( $payable) ) throw new PayableNullException("Save require Gjae\LaravelPayper\Contracts\IPayable instance");
        return $this->repository->save( $this->request()->all(), $payable );
    }


    /**
     * Crea una referencia UUID unica para el registro
     *
     * @return string
     */
    public function getUniqueReference() : string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
  
        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),
  
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,
  
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,
  
        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
      );
    }


    public function checkNullValue(string $index, string $alias = null){
        $alias = (is_null($alias))? $index : $alias;

        if( is_null( $this->config[ $index ] ) )
            throw new ConfigException("Not {$alias} config available");

        return $this->config[$index];
    }

    public function callback(string $reference = '')
    {
        return url( config('payper.callback') );
    }


}