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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('full_name');
            $table->string('password');
            $table->dateTime('expire_at')->nullable();
            $table->string('line_notify_token', 512)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('abilities', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 80)->unique();
            $table->string('label', 80)->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 80)->unique();
            $table->string('label', 80)->nullable();
            $table->timestamps();
        });

        Schema::create('ability_role', function (Blueprint $table) {
            $table->primary(['ability_id', 'role_id']);
            $table->unsignedSmallInteger('ability_id');
            $table->foreign('ability_id')->references('id')->on('abilities');
            $table->unsignedSmallInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->primary(['user_id', 'role_id']);
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedSmallInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
