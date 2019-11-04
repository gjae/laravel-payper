<?php

Route::namespace('Gjae\LaravelPayper\Controllers')->group(function(){
    Route::prefix('payper')->group(function(){

        Route::get('/', 'PayperPaymentsController@show_view')->name('payper-form');
        Route::post('make-payment', 'PayperPaymentsController@make_payment')->name('payper-payment');
        Route::get('payper-success/{reference}', 'PayperPaymentsController@success')->name('success');
        Route::get('payper-failure/{reference?}', 'PayperPaymentsController@failure')->name('failure');
        Route::get('payper-pending/{reference?}', 'PayperPaymentsController@pending')->name('pending');
    });

});