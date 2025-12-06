<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('lead_status_id')->nullable()->constrained('lead_statuses')->onDelete('set null');
            $table->string('activity_type');
            $table->text('remark')->nullable();
            $table->longText('description')->nullable();
            $table->date('date');
            $table->date('followup_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};

