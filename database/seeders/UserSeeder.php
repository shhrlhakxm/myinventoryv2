<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ahmad',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin',
        ]);

        User::create([
            'name' => 'Shahrul',
            'email' => 'user@test.com',
            'password' => Hash::make('user123'),
            'role' => 'staff',
        ]);
    }
}
