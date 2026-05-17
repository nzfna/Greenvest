<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_bans', function (Blueprint $table) {
            $table->id();
            $table->string('device_fingerprint', 64)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('banned_at');
            $table->timestamp('expires_at')->nullable(); // NULL = permanent
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_bans');
    }
};
