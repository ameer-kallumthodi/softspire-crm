<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->string('country_code');
            $table->string('phone');
            $table->foreignId('country_id')->constrained()->onDelete('restrict');
            $table->foreignId('purpose_id')->constrained()->onDelete('restrict');
            $table->unsignedBigInteger('telecaller_id')->nullable();
            $table->string('email')->nullable();
            $table->date('converted_date');
            $table->unsignedBigInteger('converted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
