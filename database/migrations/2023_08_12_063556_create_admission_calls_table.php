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
        Schema::create('admission_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('an')->index();
            $table->boolean('found')->default(false)->index();
            $table->unsignedTinyInteger('retry')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_calls');
    }
};
