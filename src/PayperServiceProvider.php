<?php
namespace Gjae\LaravelPayper;

use Illuminate\Support\ServiceProvider;

use Gjae\LaravelPayper\Payper;
use Gjae\LaravelPayper\Models\PayperPayment;
use Gjae\LaravelPayper\Classes\PayperGateway;

use Gjae\LaravelPayper\Contracts\PaymentContract;
use Blade;
class PayperServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('Payper', function($app){
            return new Payper( config('payper'),  $app->make('request') );
        }); 

        $this->app->bind('Gjae\LaravelPayper\Contracts\PaymentContract', function($app){
            return PayperPayment::whereReference( request('reference', 'asdasd') )->firstOr(function(){
                return new class implements PaymentContract {
                    public $reference = "Not reference";
                    public $id        = "Not ID";
                    public $tran_nbr  = "Not TRAN BANK";
                };
            });
        });

        $this->app->bind('Gjae\LaravelPayper\Contracts\GatewayInterface', function($app){
            $paymentManagement = PayperPayment::whereReference( request('reference', 'asdasd') )->firstOrFail();
            $gateway = new PayperGateway( $paymentManagement, config('payper.access_token'), request() );
            return $gateway;
        });
    }



    public function boot()
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->publishView();
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        /**
         * Si no se pasa un valor a la descripcion queda como nula
         * se verifica primero que se le haya pasado un valor a la descripcion al momento de haber llamado el metodo begin
         */
        Blade::directive('payper_form', function($description = null){
            $description_payper_class = app()->make('Payper')->getDescription();

            $description = is_null($description_payper_class) ? $description : $description_payper_class;

            return Blade::compileString("@include('vendor.payper.payper_form', ['description' => '{$description}' ])");
        });
        
        Blade::directive('payper_url', function(){
            return config('payper.checkout_url');
        });
    }


    public function publishMigrations()
    {
        $this->publishes([
            __DIR__.'/../migrations'    => database_path('migrations')
        ], 'migrations');
    }

    public function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/payper.php'     => config_path('payper.php')
        ], 'config');
    }

    public function publishView()
    {
        $this->publishes([
            __DIR__.'/../views'        => resource_path('views/vendor/payper'),
            __DIR__.'/../assets'        => public_path('vendor/laravelpayper')
        ], 'resources');
    }

} 