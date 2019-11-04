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
            $table->string('auth_guid', 200)->default('ESP')->index();
            $table->string('auth_resp', 10)->default('--')->index();
            $table->string('batch_id', 50)->default("--")->index();
            $table->string('response_description', 200)->nullable();
            $table->string('tran_nbr', 100)->default('--00--')->index();
            $table->string('response_code', 100)->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 12,2)->default(0.00);
            $table->string('reference', 200)->index();
            $table->json('extra_data')->nullable();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('payper_payments');
    }
}