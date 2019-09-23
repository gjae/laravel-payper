<?php 

namespace Gjae\LaravelPayper;

use Gjae\LaravelPayper\Contracts\IPayer;
use Gjae\LaravelPayper\Contracts\IPayable;
use Gjae\LaravelPayper\Contracts\PayperInterface;
use Gjae\LaravelPayper\Contracts\PayperRepository;
use Gjae\LaravelPayper\Models\PayperPayment;

// Exceptions

use Gjae\LaravelPayper\Exceptions\PayperConfigException as ConfigException;
use Gjae\LaravelPayper\Exceptions\PayableNullException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;

use Closure;
class Payper implements PayperInterface{

    /**
     * La configuracion global de la libreria
     * especificada el el archivo config/payper.php
     *
     * @var array
     */
    private $config     = null;
    
    /**
     * El repositorio que hace referencia al modelo
     * de la BD
     *
     * @var Gjae\LaravelPayper\Contracts\PayperRepository
     */
    private $repository  = null;

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

    public function __construct(array $config = null, PayperRepository $model, Request $request ){
        if( is_null($config) )
            throw new ConfigException("No config available");

        $this->config        = $config;
        $this->repository    = $model;
        $this->request       = $request;
        $this->reference     = $this->getUniqueReference();
    }

    /**
     * Retorno de la clave MD5 
     *
     * @return string
     */
    public function getPayperMD5Key() : string
    {
        return $this->checkNullValue('llavemd5', 'md5 key');
    }

    /**
     * Retorna el usuario payper especificado en el archivo de configuracion
     *
     * @return string
     */
    public function getPayperUser() : string
    {
        return $this->checkNullValue('usuario', 'payper user');
    }

    /**
     * Retorna el tipo de moneda especificado en el 
     * archivo de configuraciones
     *
     * @return string
     */
    public function getPayperCurrency() : string
    {
        return $this->checkNullValue('moneda', 'currency');
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
     * URL del checkout especificado en el archivo
     * de configuracion
     *
     * @return string
     */
    public function getPayperURLCheckout() : string
    {
        return $this->checkNullValue('checkout_url', 'checkout url');
        
    }

    public function getPayperURLBack() : string 
    {
        $aditionalData = $this->getAditionalDataAsQueryString();

        $aditionalData = is_null($aditionalData) ? '' : '?'.$aditionalData;

        $urlback = $this->checkNullValue('url_back', 'checkout url');
        
        return $urlback.$aditionalData;
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
    public function begin(Closure $function)
    {
        $function($this);
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


}