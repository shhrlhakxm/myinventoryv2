<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Notifications\WelcomeNewStaffNotification;
use Illuminate\Support\Facades\Notification;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_user_management(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/users');

        $response->assertForbidden(); // shorthand for assertStatus(403)
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete("/users/{$admin->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin1)->delete("/users/{$admin2->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin2->id]);
    }

    public function test_admin_can_delete_staff(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($admin)->delete("/users/{$staff->id}");

        $response->assertRedirect('/users');
        $response->assertSessionHas('status', "User has been deleted.");
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    public function test_admin_cannot_delete_superadmin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->delete("/users/{$superadmin->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $superadmin->id]);
    }

    public function test_admin_can_update_staff_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($admin)->patch("/users/{$staff->id}/role", [
            'role' => 'admin',
        ]);

        $response->assertRedirect('/users');
        $response->assertSessionHas('status', "{$staff->name}'s role successfully updated to Admin.");
        $this->assertDatabaseHas('users', ['id' => $staff->id, 'role' => 'admin']);
    }

    public function test_admin_cannot_update_superadmin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->patch("/users/{$superadmin->id}/role", [
            'role' => 'staff',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $superadmin->id, 'role' => 'superadmin']);
    }

    public function test_admin_cannot_update_own_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->patch("/users/{$admin->id}/role", [
            'role' => 'staff',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);
    }



    public function test_creating_staff_sends_welcome_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'New Staff',
            'email' => 'newstaff@test.com',
            'role' => 'staff',
        ]);

        // Cari user yang baru dicreate
        $newUser = User::where('email', 'newstaff@test.com')->first();

        Notification::assertSentTo($newUser, WelcomeNewStaffNotification::class);
    }
}
