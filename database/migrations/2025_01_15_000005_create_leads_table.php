<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country_code');
            $table->string('phone');
            $table->foreignId('country_id')->constrained()->onDelete('restrict');
            $table->foreignId('purpose_id')->constrained()->onDelete('restrict');
            $table->foreignId('lead_status_id')->constrained('lead_statuses')->onDelete('restrict');
            $table->foreignId('lead_source_id')->constrained('lead_sources')->onDelete('restrict');
            $table->tinyInteger('is_meta')->default(0);
            $table->unsignedBigInteger('meta_lead_id')->default(0);
            $table->unsignedBigInteger('telecaller_id')->nullable();
            $table->string('email')->nullable();
            $table->tinyInteger('is_converted')->default(0);
            $table->date('followup_date')->nullable();
            $table->date('date');
            $table->text('remarks')->nullable();
            $table->timestamp('first_created_at')->useCurrent();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['country_code', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

