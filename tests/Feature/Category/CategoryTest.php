<?php

namespace Tests\Feature\Category;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_admin_can_create_a_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Electronics',
            'code' => 'ELEC',
            'description' => 'Electronic items',
        ]);

        $response->assertRedirect('/categories');
        $response->assertSessionHas('success', 'Category created successfully.');
        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'code' => 'ELEC',
            'description' => 'Electronic items',
        ]);
    }

    public function test_admin_cannot_delete_a_category_that_contains_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        Item::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->delete("/categories/{$category->id}");

        $response->assertRedirect('/categories');
        $response->assertSessionHas('error', 'Cannot delete category with associated items.');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
