<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->string('transfer_cost')->nullable();
        $table->string('phone');
        $table->string('payment_status')->nullable();
        $table->string('pay');
        $table->string('second_phone')->nullable();
        $table->string('payment_type')->nullable();
        $table->string('shipping_type')->nullable();
        $table->datetime('payed_date')->nullable();
        $table->string('logistic_type')->nullable();
        $table->text('add_content')->nullable();
        $table->text('content')->nullable();
        $table->text('payment_content')->nullable();
        $table->foreignId('bairshil_id')->nullable()->constrained('bairshils');
        $table->foreignId('user_id')->nullable()->constrained('users');
        $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};