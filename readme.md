### Instalación

> composer require gjae/laravel-payper

### Instalación

en el archivo config/app.php, dentro del arreglo de $providers, agregue la siguiente linea:

```php
    Gjae\LaravelPayper\PayperServiceProvider::class,
```

luego en el array $aliases, agregue la siguiente linea

```php
    Gjae\LaravelPayper\Facade::class,
```

por ultimo para terminar la instalación ejecute la siguiente linea:

> php artisan vendor:publish --provider="Gjae\LaravelPayper\PayperServiceProvider"

ahora con la linea anterior un podra ver una nueva migración, la cual ser la que se encargara de guardar los datos necesarios,
también se agregara un archivo config/payper.php con las configuraciones que debera completar, de lo contrario se indicara la falta
con una excepción de tipo 
```php
    PayperConfigException
```

### Uso basico

La libreria puede aplicarse a cualquier objeto que pueda generar pagos (facturas, boletas, etc), unicamente debe implementar una interfaz y un trait de la siguiente manera:

```php

use Gjae\LaravelPayper\Contracts\IPayable;
use Gjae\LaravelPayper\Payable;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model implements IPayable{

    use Payable;
    protected $table = 'facturas';
}

```

una vez aplicado, debera crear la ruta de su urbback (y agregado en la configuración).


#### Iniciando la transacción

```php

// Para iniciar una transaccion se debe llamar al metodo begin del facade Payper:
use Payper;

Payper::begin(function($transaccion){
    
    // Agregar el valor de la transaccion a realizar
    $transaccion->setValor(1000);

    // (OPCIONAL) , si desea agregar parametros adicionales para enviarlos a payper
    $transaccion->setAditionalData(['param1' => 'value1', ]);
    
});

```
#### Importando el formulario

La libreria automaticamente generara el formulario que sera enviado a payper, unicamente debe invocar la directiva 
>> @payper_form(DESCRIPCIÓN DE LA TRANSACCION QUE SE REFLEJARA EN PAYPER)

mientras que para llamar a la url del checkout puede hacer uso de la directiva
>> @payper_url

por ejemplo:

```php
<form action="@payper_url" method="POST">
    @payper_form(Compra de tenedor para ensalada)

    <input type="submit" value="Enviar">
</form>

```

#### Para finalizar

La libreria payper toma automaticamente los datos de respuesta del servidor de payper, usted unicamente debe invocar al  metodo save del facade Payper y pasar como unico agumento, el objeto del modelo que se relaciona (aquel que implemente el contrato IPayable y haga uso del trait Payable) con el pago, por ejemplo:

```php

use App\Factura;

$factura = Factura::first();
Payper::save( $factura );

```