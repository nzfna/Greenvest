<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admingreenvest@gmail.com'],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make('Admin#1234'),
                'email_verified_at' => now(),
            ]
        );
    }
}