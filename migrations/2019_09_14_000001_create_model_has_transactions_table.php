<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelHasTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('model_has_transactions', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('payper_payment_id')->references('id')->on('payper_payments')->onDelete('cascade');
            $table->string('model_type', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('model_has_transactions');
    }
}