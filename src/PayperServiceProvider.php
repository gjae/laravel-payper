<?php
namespace Gjae\LaravelPayper;

use Illuminate\Support\ServiceProvider;

use Gjae\LaravelPayper\Payper;

class PayperServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('Payper', function($app){
            return new Payper( config('payper') );
        }); 
    }


    public function boot()
    {
        $this->publishConfig();
        $this->publishMigrations();
    }


    public function publishMigrations()
    {
        $this->publishes([
            __DIR__.'/../migrations'    => database_path('migrations')
        ]);
    }

    public function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/payper.php'     => config_path('payper.php')
        ]);
    }
} 