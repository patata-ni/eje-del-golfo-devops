<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('mobile_reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('departure_id');
            $table->foreign('departure_id')->references('id_salida')->on('salidas')->restrictOnDelete();
            $table->unsignedSmallInteger('seat');
            $table->uuid('request_id');
            $table->json('snapshot');
            $table->timestamps();
            $table->unique(['departure_id', 'seat']);
            $table->unique(['user_id', 'request_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_reservations');
        Schema::dropIfExists('personal_access_tokens');
    }
};
