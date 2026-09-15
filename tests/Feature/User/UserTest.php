<?php

namespace Tests\Feature\User;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Notifications\WelcomeNewStaffNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

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
        $response->assertSessionHas('status', 'User has been deleted.');
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

    public function test_superadmin_cannot_delete_own_account_through_user_management(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($superadmin)->delete("/users/{$superadmin->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $superadmin->id, 'role' => 'superadmin']);
    }

    public function test_superadmin_cannot_update_own_role(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($superadmin)->patch("/users/{$superadmin->id}/role", [
            'role' => 'staff',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $superadmin->id, 'role' => 'superadmin']);
    }

    public function test_superadmin_can_update_another_users_role(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($superadmin)->patch("/users/{$admin->id}/role", [
            'role' => 'staff',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'staff']);
    }

    public function test_superadmin_can_delete_another_admin(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($superadmin)->delete("/users/{$admin->id}");

        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_admin_can_create_staff_who_sets_password_and_logs_in(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $newPassword = 'SecurePassword123!';

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New Staff',
            'email' => 'newstaff@test.com',
            'role' => 'staff',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.index'));

        $newUser = User::where('email', 'newstaff@test.com')->sole();

        $this->assertDatabaseHas('users', [
            'id' => $newUser->id,
            'name' => 'New Staff',
            'email' => 'newstaff@test.com',
            'role' => 'staff',
        ]);
        $this->assertFalse(Hash::check('', $newUser->password));

        $invitationToken = null;

        Notification::assertSentTo(
            $newUser,
            WelcomeNewStaffNotification::class,
            function (WelcomeNewStaffNotification $notification, array $channels) use (&$invitationToken): bool {
                $invitationToken = $notification->token;

                return in_array('mail', $channels, true);
            }
        );

        $this->assertNotNull($invitationToken);

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();

        $resetResponse = $this->post(route('password.store'), [
            'token' => $invitationToken,
            'email' => $newUser->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $resetResponse
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $newUser->refresh();
        $this->assertTrue(Hash::check($newPassword, $newUser->password));

        $loginResponse = $this->post(route('login'), [
            'email' => $newUser->email,
            'password' => $newPassword,
        ]);

        $loginResponse->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($newUser);
    }

    public function test_admin_cannot_delete_staff_with_inventory_transactions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $category = Category::create(
            [
                'name' => 'Test Category',
                'code' => 'TEST-CAT',
                'description' => 'A category for testing purposes.',
            ]
        );
        $item = Item::create(
            [
                'category_id' => $category->id,
                'name' => 'Test Item',
                'description' => 'A item for testing purposes.',
                'sku' => 'TEST-001',
                'price' => 10.99,
            ]
        );

        $staff->inventoryTransactions()->create([
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 10,
            'notes' => 'Initial stock',
        ]);

        $response = $this->actingAs($admin)->delete("/users/{$staff->id}");

        $response->assertRedirect('/users');
        $response->assertSessionHas('error', 'Cannot delete a user who has recorded inventory transactions.');
        $this->assertDatabaseHas('users', ['id' => $staff->id]);
        $this->assertDatabaseHas('inventory_transactions', [
            'user_id' => $staff->id,
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 10,
        ]);
    }
}
