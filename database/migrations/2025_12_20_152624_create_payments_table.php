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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->string('transaction_id')->nullable();
            $table->enum('payment_type', ['online', 'cash', 'bank_transfer', 'cheque', 'other'])->default('online');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->string('payment_number')->unique();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
