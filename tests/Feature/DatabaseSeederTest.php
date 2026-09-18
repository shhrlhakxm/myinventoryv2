<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_can_be_seeded_repeatedly(): void
    {
        config()->set([
            'superadmin.name' => 'SuperAdmin',
            'superadmin.email' => 'superadmin@test.com',
            'superadmin.password' => 'test-superadmin-password',
        ]);
        
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('categories', 2);
        $this->assertDatabaseCount('items', 4);
        $this->assertDatabaseCount('inventory_transactions', 7);

        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@test.com',
            'role' => 'superadmin',
        ]);

        $this->assertCredentials([
            'email' => 'superadmin@test.com',
            'password' => 'test-superadmin-password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);
        $this->assertCredentials([
            'email' => 'admin@test.com',
            'password' => 'admin123',
        ]);

        $this->assertCredentials([
            'email' => 'user@test.com',
            'password' => 'user123',
        ]);

        $this->assertDatabaseHas('items', [
            'sku' => 'PKG-LID-12',
            'current_stock' => 30,
            'minimum_stock' => 40,
        ]);

        $this->assertDatabaseHas('items', [
            'sku' => 'BEV-MILK-1L',
            'current_stock' => 8,
            'minimum_stock' => 12,
        ]);
    }
}
