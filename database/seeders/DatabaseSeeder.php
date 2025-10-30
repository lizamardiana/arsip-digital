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
            'name' => 'Nabilatul Fikrah S.IP',
            'email' => 'nabilatulfkrh@gmail.com',
            'nip' => '199203052015072003',
            'jabatan' => 'Staff Bidang PU',
            'password' => Hash::make('password123'),
            'phone' => '081234567890',
            'address' => 'Jl. Jend. Basuki Rachmat No. 1, Telanaipura, Kota Jambi, Jambi 36124',
            'is_active' => true,
        ]);

    }
}