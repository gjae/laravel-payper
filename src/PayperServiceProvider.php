<?php
namespace Gjae\LaravelPayper;

use Illuminate\Support\ServiceProvider;

use Gjae\LaravelPayper\Payper;
use Gjae\LaravelPayper\Repositorys\PayperRepository;

use Blade;
class PayperServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('Payper', function($app){
            return new Payper( config('payper'), new PayperRepository, $app->make('request') );
        }); 

        $this->app->bind(
            'Gjae\LaravelPayper\Contracts\PayperRepository',
            'Gjae\LaravelPayper\Repositorys\PayperRepository'
        );
    }


    public function boot()
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->publishView();

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
            $aditional = app()->make('Payper')->getAditionalDataAsQueryString() == null 
            ? '' 
            : '?'.app()->make('Payper')->getAditionalDataAsQueryString();
            
            return config('payper.checkout_url').$aditional;
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
        ]);
    }

    public function publishView()
    {
        $this->publishes([
            __DIR__.'/../views'        => resource_path('views/vendor/payper')
        ], 'resources');
    }
} 