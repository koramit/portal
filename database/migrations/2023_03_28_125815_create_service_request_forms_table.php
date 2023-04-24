<?php

use App\Models\User;
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
        Schema::create('service_request_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'requester_id');
            $table->jsonb('form');
            $table->foreignIdFor(User::class, 'authority_id')->nullable();
            $table->foreignIdFor(User::class, 'revoke_authority_id')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_forms');
    }
};
