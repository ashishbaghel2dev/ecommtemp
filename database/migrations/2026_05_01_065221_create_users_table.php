<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\UserRole;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->index();

            // Auth
            $table->string('password');
            $table->rememberToken();

            // Role & Status
            $table->string('role')->default(UserRole::USER->value)->index();
            $table->boolean('status')->default(true)->index();

            // Verification
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            // Profile
            $table->string('avatar')->nullable();

            // Security
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            // Soft Delete
            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
