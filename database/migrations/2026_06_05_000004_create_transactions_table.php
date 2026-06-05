<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->unique();
            $table->date('booked_date')->nullable();
            $table->date('trade_date')->nullable();
            $table->date('settlement_date')->nullable();
            $table->string('portfolio')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('security_name')->nullable();
            $table->string('isin')->nullable();
            $table->decimal('quantity', 20, 6)->nullable();
            $table->decimal('price', 20, 6)->nullable();
            $table->decimal('interest', 20, 6)->nullable();
            $table->decimal('total_fees', 20, 6)->nullable();
            $table->string('fees_currency')->nullable();
            $table->decimal('amount', 20, 6)->nullable();
            $table->string('amount_currency')->nullable();
            $table->decimal('purchase_value', 20, 6)->nullable();
            $table->string('purchase_value_currency')->nullable();
            $table->decimal('result', 20, 6)->nullable();
            $table->string('result_currency')->nullable();
            $table->decimal('total_quantity', 20, 6)->nullable();
            $table->decimal('balance', 20, 6)->nullable();
            $table->decimal('exchange_rate', 20, 6)->nullable();
            $table->text('transaction_text')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->string('contract_note_number')->nullable();
            $table->string('verification_number')->nullable();
            $table->decimal('brokerage', 20, 6)->nullable();
            $table->string('brokerage_currency')->nullable();
            $table->decimal('currency_rate', 20, 6)->nullable();
            $table->decimal('initial_interest', 20, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
