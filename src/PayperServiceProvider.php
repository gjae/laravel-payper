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
        Blade::directive('payper_form', function($description){
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