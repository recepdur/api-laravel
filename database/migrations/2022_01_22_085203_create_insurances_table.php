<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->boolean('status')->default(true);
			$table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('plate_no', 20)->nullable();
			$table->string('car_register_no', 30)->nullable();
            $table->string('company', 50)->nullable();
            $table->string('policy_no', 30)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('commission_rate')->nullable();
            $table->decimal('gross_price', 10, 2)->nullable();
            $table->decimal('net_price', 10, 2)->nullable(); 
            $table->decimal('commission_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insurances');
    }
}
