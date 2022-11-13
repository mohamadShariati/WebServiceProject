<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users','id')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders','id')->onDelete('cascade');
            $table->unsignedInteger('amount');
            $table->string('token')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('trans_id')->nullable();
            $table->string('request_from')->default('web');

            $table->softDeletes();
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
        Schema::dropIfExists('transactions');
    }
};
