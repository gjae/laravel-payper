### Instalación

> composer require gjae/laravel-payper


en el archivo config/app.php, dentro del arreglo de $providers, agregue la siguiente linea:

```php
    ...
    Gjae\LaravelPayper\PayperServiceProvider::class,
```

luego en el array $aliases, agregue la siguiente linea

```php
    ...
    Gjae\LaravelPayper\Facade::class,
```

por ultimo para terminar la instalación ejecute la siguiente linea:

> php artisan vendor:publish --provider="Gjae\LaravelPayper\PayperServiceProvider"

a partir de este punto, notara ahora el archivo config/payper.php, 2 migraciones nuevas y archivos CSS Y JS en su carpeta public/vendor/laravelpayper ademas de 3 vistas nuevas en resources/views/vendor, estas ultimas son de base y le ayudara de guia en el proceso de pago (aunque puede modificarlas a gusto apra personalizarlas).

Ejecución de las migraciones

> php artisan migrate

### Uso basico

##### Archivo de configuración
Primeramente; abra su archivo de configuracion en config/payper.php y modifiquelo segun sus necesidades, breve explicación de cada linea de su archivo de configuración:

```php
    'debug_mode'        => true,    
```
Habilita el modo debug de su instalación de la librería, cuando esta variable esta en true, las unicas tarjetas validas son aquellas que sse encuentran en "debug_cards" , en caso contrario (si esta en false),  entonces la libreria emitira un error si la tarjeta ingresada para procesar el pago no se encuentra registrada en el atributo "debug_cards".

```php
    'origin'            => env('PAYPER_ORIGIN', ''),
```
Origen de la petición, por ejemplo : wwww.example-domain.com

```php
    'access_token'      => env('PAYPER_ACCESS_TOKEN', ''),
```
Token de acceso emitido por PAYPER, se recomienda crear la variable PAYPER_ACCESS_TOKEN en su archivo .env y desde ahi asignarle su valor 

```php 
    'porcentaje_impuesto' => env('PAYPER_TAX_PERCENT', null),
```
Porcentaje de impuesto (IVA por ejemplo)

```php
    'debug_cards'           => [

    ],
```
Tarjetas validas en el proceso de desarrollo, si la variable "debug_mode" se encuentra como true, unicamente se procesaran las tarjetas agregadas en este arreglo

```php
    'transaction_case_routes'   => [

        'success'               => '',


        'failure'               => '',


        'pending'               => ''

    ],
```
Posibles caso de rutas en caso de ... (transacción satisfactoria, fallida o pendiente), se recomienda usar el metodo name de las rutas, en caso de permanecer estas rutas en blanco, la libreria ejecutara rutas por defecto que enviaran a las vistas dentro de resources/views/vendor/payper

```php
    'callback'                   => ''
```
Ruta de redirección al completar el proceso, el usuario la define y debera llamar el metodo callback del facade Payper para retornar la URL 

##### Funcionamiento

Para iniciar una transacción unicamente debe llamar al metodo begin del facade Payper, recibe como primer parametro un callback que a su vez recibe el objeto de la transacción propiamente y retorna una instancia con los datos completamente configurados y la instancia con el modelo en la BD pre-insertado. A continuación una vista rapida de sus principales metodos:

```php
    $txInstance = Payper::begin(function($trx){

        // Ingresar el valor de la transacción
        $trx->setValor( 100.00 ); 

        // Descripción de la transacción
        $trx->setDescription("Descripción de la transacción");

        // (OPCIONAL) datos extras de la transacción, recibe un arreglo con los datos adicionales de la transacción que quiera guardar
        $trx->setAditionalData([ 'foo' => 'bar', 'foo1' => [ 'foo.1' => 'bar-1' ] ]);

        // (OPCIONAL) en caso de querer agregar una referencia propia a la transacción
        // NOTA: La libreria genera una referencia automatica unica para la transacción en forma de UUID
        $trx->setReference("MY-AWESOME-REFERENCE");

        // (OPCIONAL) En caso de querer agregar un porcentaje de impuesto 
        // La libreria busca primero en la configuración, sin embargo si utiliza este metodo puede sobreescribir (para la transacción actual) el porcentaje de impuesto
        // NOTA: Si no se configura un porcentaje de impuesto bien sea por el archivo de configuracion o de manera manual. la libreria emitira una excepcion de tipo PayperConfigException
        $trx->setPayperTax(10);
    });

    // Por ultimo retorne la vista con el formulario para realizar el pago
    return view('vendor.payper.payper_form', ['transaction' => $trxInstance]);
```
Una vez sea completado el proceso de pago la libreria de payper retornara la vista dependiendo de su configuración para los 3 tipos de casos posibles en una transacción.


##### Relacionando pagos con otros modelos

Para relacionar los pagos de payper con otros modelos de la aplicación, unicamente debe cumplir los siguientes requerimientos:
- Implementar el contrato Gjae\LaravelPayper\Contracts\HasTransaction
- Implementar el trait Gjae\LaravelPayper\Traits\HasTransactions

ejemplo: 
```php

...
use Gjae\LaravelPayper\Contracts\HasTransaction;
use Gjae\LaravelPayper\Traits\HasTransactions;
class class User extends Authenticatable implements HasTransaction
{
    use HasTransactions;
    ...
}

```
Luego de hacer esto, unicamente necesita pasar como segundo argumento a la funcion begin del facade Payper, una instancia del modelo que implementa el contrato y el trait, ejemplo:

```php

$user = User::first();
Payper::begin(..., $user);
```

Una transacción puede relacionarse con mas de un objeto que implemente el contrato HasTransaction, independientemente el modelo que lo implemente, unicamente debe pasar un array de objetos como segundo parametro del metodo begin, ejemplo:

```php

$user1 = User::find(1);
$user2 = User::find(2);

Payper::begin(..., [$user1, $user2]);

```

##### Recuperando las transacciones de un modelo 

Puede recuperar todas las transacciones relacionadas a un modelo llamando al metodo transactions de la clase que utilice el trait HasTransactions, por ejemplo:

```php
$user = User::first();
$user->transactions;
```

### Trabajando la transacción de manera personalizada

La libreria ya provee una manera de ejecutar la transacción de manera automatica; en el formulario del archivo resources/views/vendor/payper/payper_form.blade.php, vera que el action esta destinado a la ruta con el nombre 'payper-payment'

```html
    <form action="{{ route('payper-payment') }}" method="POST" id="pay-form">

    </form>
```
ademas contiene un input oculto cuyo valor es la referencia de la transacción
```html
    <input type="hidden" name="reference" value="{{ $transaction->getReference() }}">
```
Este formulario esta destinado a ser personalizado (si se desea), el formulario sera dirigido al controlador por defecto de la libreria, el usuario puede alterar este funcionamiento creando su propio controlador e inyectando al metodo del controlador, una instancia del contrato GatewayInterface, el cual se encarga de procesar la transacción

```php

...
use Gjae\LaravelPayper\Contracts\GatewayInterface;

class PayperPaymentsController extends Controller
{
    ...

    public function procesar_transaccion(GatewayInterface $transactionManager)
    {
        ...
        return $transactionManager->exec()->redirectTo([ 'foo' => 'bar' ]);
    }
}

```

La instancia del contrato expone los siguientes metodos
```php
    public function getAuthResponse();           // Retorna el codigo de autorización generado por PAYPER
    public function getBatchId();               // Devuelve el batch_id de la transacción
    public function getExtraData();      // Devuelve los datos adicionales de la transacción (agregados con el metodo setAditionalData)
    public function getResponseDescription();   // Devuelve la descripción de la respuesta
    public function getTranNbr();               // Devuelve el codigo de transacción del banco

    public function exec();             // Ejecuta la transacción con la api del servidor de PAYPER y retorna la instancia en si misma
    public function redirectTo(array $routeParams); // Devuelve la ruta segun el caso, configurados en el archivo config/payper.php   
```
