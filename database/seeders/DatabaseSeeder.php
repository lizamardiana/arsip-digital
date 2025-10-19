<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@arsipsurat.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh Alamat No. 123',
            'is_active' => true,
        ]);

    }
}