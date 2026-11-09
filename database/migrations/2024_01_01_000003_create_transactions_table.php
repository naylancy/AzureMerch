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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 64)->unique()->index();
            $table->foreignId('cashier_id')->constrained('users')->onDelete('restrict');
            $table->string('customer_name')->default('Walk-in Customer');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2);
            $table->decimal('change_amount', 14, 2)->default(0);
            $table->string('payment_method', 20)->default('cash'); // 'cash', 'qris', 'transfer', 'debit'
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('completed'); // 'completed', 'cancelled'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
