<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminEmail = config('superadmin.email');
        $superAdminPassword = config('superadmin.password');

        if (filled($superAdminEmail) && filled($superAdminPassword)) {
            User::firstOrCreate(
                ['email' => $superAdminEmail],
                [
                    'name' => config('superadmin.name'),
                    'password' => Hash::make($superAdminPassword),
                    'role' => 'superadmin',
                ],
            );
        }

        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Ahmad',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
        );

        User::updateOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Shahrul',
                'password' => Hash::make('user123'),
                'role' => 'staff',
            ],
        );
    }
}
