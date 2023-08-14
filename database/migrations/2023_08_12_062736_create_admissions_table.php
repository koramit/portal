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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('an')->index();
            $table->unsignedInteger('hn')->index();
            $table->string('name', 128)->index();
            $table->date('dob')->nullable()->index();
            $table->unsignedTinyInteger('gender')->index();
            $table->string('discharge_type_name', 100)->nullable()->index();
            $table->string('discharge_status_name', 100)->nullable()->index();
            $table->dateTime('admitted_at')->nullable()->index();
            $table->dateTime('discharged_at')->nullable()->index();
            $table->foreignId('ward_id')->constrained('wards')->onDelete('cascade');
            $table->foreignId('attending_staff_id')->constrained('attending_staffs')->onDelete('cascade');
            $table->timestamp('checked_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
