<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('FILAMENT_ADMIN_EMAIL')],
            [
                'name' => 'Admin',
                'email' => env('FILAMENT_ADMIN_EMAIL'),
                'password' => Hash::make(env('FILAMENT_ADMIN_PASSWORD')),
            ]
        );
    }
}
