<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PayperPayments extends Migration
{
    public function up()
    {
        Schema::create('payper_payments', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('referencia', 100)->index();
            $table->char('moneda', 3)->default('USD');
            $table->decimal('valor', 12, 2)->default(0.00);
            $table->enum('respuesta', ['APROBADA', 'RECHAZADA', 'PENDIENTE'])->default('PENDIENTE');
            $table->string('cuentanro',  100)->index();
            $table->enum('metodousado', ['TC', 'WU', 'DEPOSITO']);
            $table->string('autorizacion', 100)->index();
            $table->string('nrotransaccion', 100)->index();

            $table->string('payable_type')->nullable();
            $table->integer('payable_id')->default(0);
        });
    }
}