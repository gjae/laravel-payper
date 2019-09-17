<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayperPaymentsTable extends Migration
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
            $table->string('cuentanro',  100)->default('0000ESP')->index();
            $table->enum('metodousado', ['TC', 'WU', 'DEPOSITO']);
            $table->string('autorizacion', 100)->default("0000ESP")->index();
            $table->string('nrotransaccion', 100)->default('0000ESP')->index();

            $table->string('payable_type')->nullable();
            $table->integer('payable_id')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payper_payments');
    }
}