<?php 

namespace Gjae\LaravelPayper;

use Gjae\LaravelPayper\Contracts\IPayer;
use Gjae\LaravelPayper\Contracts\IPayable;
use Gjae\LaravelPayper\Contracts\PayperInterface;

// Exceptions

use Gjae\LaravelPayper\Exceptions\PayperConfigException as ConfigException;
class Payper implements PayperInterface{

    private $config = null;

    public function __construct(array $config = null){
        
        if( is_null($config) )
            throw new ConfigException("No config available");

    }


}