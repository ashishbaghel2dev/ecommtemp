<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegistrationRedirectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->index();
            $table->string('password');
            $table->rememberToken();
            $table->string('role')->default('user')->index();
            $table->boolean('status')->default(true)->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_verified_registration_redirects_to_login_without_authenticating_user(): void
    {
        $response = $this
            ->withSession([
                'register_data' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'phone' => '919876543210',
                    'password' => Hash::make('password123'),
                    'redirect_to' => null,
                    'otp' => '123456',
                    'otp_expires_at' => now()->addMinutes(10),
                ],
            ])
            ->post(route('register.otp.verify'), [
                'otp' => '123456',
            ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'phone' => '919876543210',
        ]);
    }
}
