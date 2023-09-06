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
        Schema::table('service_access_logs', function (Blueprint $table) {
            $table->foreign('personal_access_token_id')
                ->references('id')
                ->on('personal_access_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_access_logs', function (Blueprint $table) {
            $table->dropForeign('service_access_logs_personal_access_token_id_foreign');
        });
    }
};
