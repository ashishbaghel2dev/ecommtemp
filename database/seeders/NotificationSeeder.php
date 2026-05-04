<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // get first user (important)
        $user = User::first();

        if (!$user) {
            $this->command->warn('No user found. Please create user first.');
            return;
        }

        // insert multiple notifications
        Notification::insert([
            [
                'user_id' => $user->id,
                'title' => 'Welcome',
                'message' => 'Welcome to dashboard',
                'type' => 'info',
                'show_from' => now(),
                'show_until' => now()->addHours(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Order Placed',
                'message' => 'Your order #123 has been placed',
                'type' => 'success',
                'show_from' => now(),
                'show_until' => now()->addHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Payment Failed',
                'message' => 'Your payment failed, retry again',
                'type' => 'error',
                'show_from' => now(),
                'show_until' => now()->addHours(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
