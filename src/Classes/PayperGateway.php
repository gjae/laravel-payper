<?php

namespace Gjae\LaravelPayper\Classes;

use Illuminate\Http\Request;

use Gjae\LaravelPayper\Contracts\PaymentContract;

use Gjae\LaravelPayper\Contracts\GatewayInterface;
use GuzzleHttp\Client;

use Gjae\LaravelPayper\Exceptions\PayperConfigException;
use Gjae\LaravelPayper\Exceptions\DebugModeException;
use Gjae\LaravelPayper\Exceptions\PayperErrorException;
class PayperGateway implements GatewayInterface
{
    /**
     * El modelo que se actualizara en base al pago
     *
     * @var Gjae\LaravelPayper\Models\PayperPayment
     */
    private $paymentManagement = null;

    /**
     * Token de acceso especificado en el archivo de configuracion
     *
     * @var string
     */
    private $access_token = "";


    /**
     * Datos del formulario para realizar el pago
     *
     * @var string
     */
    private $form_data = null;


    private $transaction_status = '';

    public function __construct(PaymentContract $paymentManagement, string $access_token, Request $request)
    {
        $this->paymentManagement = $paymentManagement;
        $this->access_token = $access_token;
        $this->form_data = $request;
    }

    public function exec($enableExceptions = false)
    {
        $this->checkExcludeCards( $this->form_data->card_number );
        try{
            $this->checkConfigAvailable();
            $this->sendTransaction();
        }catch(\Exception $e)
        {
            $this->transaction_status = 'failure';
        }

        $this->whereException($enableExceptions);
        return $this;
    }

    /**
     * Verifica si existe alguna excepción correspondiente a alguna respuesta
     * que no sea de tipo "00
     *
     * @param boolean $enableExceptions
     * @return void
     */
    public function whereException($enableExceptions = false)
    {
        if( $enableExceptions && $this->getAuthResponse() != "00" )
            throw new PayperErrorException( $this->getResponseDescription() );

        return $this->getAuthResponse() == "00" ? false : new PayperErrorException( $this->getResponseDescription() );
    }

    /**
     * Verifica que haya una configuración valida en el
     * token de acceso y en el origin de la solicitud
     *
     * @return void
     */
    private function checkConfigAvailable()
    {

        if( empty( $this->access_token ) ) throw new PayperConfigException( 'Access token not config ');
        if( empty( $this->getOrigin() ) ) throw  new PayperConfigException( 'Origin not config on config/payper.php' );
    }

    private function getOrigin()
    {
        return config('payper.origin');
    }

    /**
     * Verifica la configuración en caso de estar en modo debug
     *
     * @param string $card
     * @return void
     */
    private function checkExcludeCards(string $card)
    {
        $debug_cards  = config('payper.debug_cards');
        $is_debug     = config('payper.debug_mode');

        if( $is_debug && !in_array( $card , $debug_cards) ) 
            throw new DebugModeException( 'Debug mode: card invalid exception' );
            
        if( !$is_debug && in_array( $card , $debug_cards) )
            throw new DebugModeException( 'Debug mode: card invalid exception' );

        return true;
    }

    /**
     * Realiza la transacción con la api 
     *
     * @return void
     */
    public function sendTransaction()
    {

        $client = new Client([
            'base_uri' => config('payper.api_url'),

            'timeout'  => 100.0,
        ]);

        $response = $client->request('POST', '', [
            'headers'           => [
                'Authorization'     => 'Bearer '.$this->access_token,
                'Content-TYpe'      => 'application/json',
                'Origin'            => $this->getOrigin(),
                'User-Agent'        => 'laravel-payper/2.0'
            ],
            'body'              => $this->rawBody()
        ]);

        $data_response = \json_decode( $response->getBody()->getContents() ); 
        foreach( $data_response as $key => $value )
        {
            if( $key == "data" ) continue;
            $this->paymentManagement->$key = $value;
        }

        $this->paymentManagement->save();

        $this->transaction_status = 'success';
    }

    /**
     * Verifica si tiene algun algun codigo de respuesta diferente a "00
     *
     * @return boolean
     */
    public function hasException()
    {
        return ! trim( $this->getAuthResponse() )== "00";
    }

    public function redirectTo(array $routeParams = [])
    {
        return redirect()->route( $this->getCallbackRoute('success', $this->transaction_status), $routeParams );
    }

    private function rawBody()
    {
        return json_encode([
            'card_number'   => str_replace(' ' ,'', $this->form_data->card_number),
            'card_name'     => $this->form_data->card_name,
            'card_cvv'      => $this->form_data->card_cvc,
            'expiration'    => str_replace(" / ", "", $this->form_data->expiration),
            'amount'        => $this->paymentManagement->amount,
            'data'          => $this->paymentManagement->extra_data
        ]);
    }


    public function getCallbackRoute($case = '', $defaultRoute = '')
    {
        $route = config('payper.transaction_case_routes.'.$case);

        return !empty( $route ) ? $route : $defaultRoute;
    }

    public function getResponseStatusCode()
    {

    }

    public function getAuthResponse()
    {
        return $this->paymentManagement->auth_resp;
    }

    public function getBatchId()
    {
        return $this->paymentManagement->batch_id;
    }

    public function getExtraData()
    {
        return $this->paymentManagement->extra_data;
    }

    public function getResponseDescription()
    {
        return trim($this->paymentManagement->response_description);
    }

    public function getTranNbr()
    {
        return $this->paymentManagement->tran_nbr;
    }


    public function setAccessToken(string $access_token)
    {
        $this->access_token = empty( $access_token ) ? $this->access_token : trim($access_token);
    }

}