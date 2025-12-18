<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->date('quotation_date');
            $table->string('duration_months', 50)->nullable();
            $table->text('technologies')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('annual_amount', 15, 2)->nullable();
            $table->string('quotation_number')->unique();
            $table->text('terms_conditions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};

