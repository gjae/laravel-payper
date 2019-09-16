<?php

return [

    /**
     * 
     */
    'usuario'           => env('PAYPER_USER', null),

    /**
     * 
     */
    'llavemd5'          => env('PAYPER_MD5_KEY', null),

    /**
     * 
     */
    'moneda'            => env('PAYPER_CURRENCY', 'USD'),

    /**
     * 
     */
    'porcentaje_impuesto' => env('PAYPER_TAX_PERCENT', null),

    /**
     * 
     */
    'checkout_url'       => 'https://www.paypersytem.com/secure/gangway/index.do',

    /**
     * 
     */
    'url_back'            => env('PAYPER_URL_BACK', null),

];