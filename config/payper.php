<?php

return [

    /**
     * Si esta variable esta en true, verifica que la tarjeta ingresada sea una tarjeta
     * agregada en el arreglo "debug_cards" , de no estarlo emite una  excepcion para indicar que la aplicación esta
     * en modo debug y no permitira la transacción con una tarjeta que no este marcada como tarjeta de prueba (debug_cards),
     * 
     * en caso de estar como false realiza el trabajo a la inversa
     * 
     * 
     */
    'debug_mode'        => true,            


    /**
     * Dominio de origen de la transacción
     * 
     * 
     */
    'origin'            => env('PAYPER_ORIGIN', ''),


    /**
     * Token de acceso emitido por payper
     * 
     * 
     */
    'access_token'      => env('PAYPER_ACCESS_TOKEN', ''),

    /**
     * porcentaje de impuesto aplicado a las transacciones
     * 
     * 
     */
    'porcentaje_impuesto' => env('PAYPER_TAX_PERCENT', null),


    /**
     * Tarjetas para usar en caso de estar el modo debug activo
     * O excluirla en caso de estar en produccion (debug_mode = false)
     * 
     */
    'debug_cards'           => [

    ],


    /**
     * Url de la api
     * 
     */
    'api_url'               => 'https://www.paypersystem.com/api/v1/transact',


    /**
     * Rutas para los 3 casos posibles, en caso de sobreescribir utilice los nameroutes ( ->name( 'nombre de la ruta' ) )
     * y usar en estas el helper route ( route('nombre de la ruta') )
     * 
     */
    'transaction_case_routes'   => [

        'success'               => '',


        'failure'               => '',


        'pending'               => ''

    ],


    /**
     * URI de redirección luego de la respuesta de la transacción
     * 
     * 
     */
    'callback'                   => ''

];