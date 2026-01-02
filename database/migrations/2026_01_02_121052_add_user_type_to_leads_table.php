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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('user_type')->nullable()->after('telecaller_id')->comment('telecaller or digital_marketing');
            $table->unsignedBigInteger('user_id')->nullable()->after('user_type')->comment('telecaller_id or digital_marketing employee id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'user_id']);
        });
    }
};
