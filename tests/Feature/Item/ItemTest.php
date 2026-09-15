<?php

namespace Tests\Feature\Item;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_authenticated_user_can_create_an_item_with_initial_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => 'Test Item',
            'sku' => 'TEST123',
            'category_id' => $category->id,
            'current_stock' => 10,
            'minimum_stock' => 5,
            'unit_price' => 99.99,
        ]);

        $response->assertRedirect('/items');
        $response->assertSessionHas('status', 'Item created successfully.');
        $this->assertDatabaseHas('items', [
            'name' => 'Test Item',
            'sku' => 'TEST123',
            'category_id' => $category->id,
            'current_stock' => 10,
            'minimum_stock' => 5,
            'unit_price' => 99.99,
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'item_id' => Item::where('sku', 'TEST123')->first()->id,
            'user_id' => $user->id,
            'type' => TransactionType::In->value,
            'quantity' => 10,
            'notes' => 'Initial stock on item creation',
        ]);
    }

    public function test_authenticated_user_cannot_delete_an_item_with_recorded_transactions(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();
        $item = Item::factory()->create(['category_id' => $category->id]);
        $inventoryTransaction = InventoryTransaction::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => TransactionType::In->value,
            'quantity' => 5,
            'notes' => 'Initial stock',
        ]);

        $response = $this->actingAs($user)->delete(route('items.destroy', $item->id));

        $response->assertRedirect('/items');
        $response->assertSessionHas('error', 'Cannot delete an item that has recorded transactions.');
        $this->assertDatabaseHas('items', ['id' => $item->id]);
        $this->assertDatabaseHas('inventory_transactions', [
            'id' => $inventoryTransaction->id,
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => TransactionType::In->value,
            'quantity' => 5,
            'notes' => 'Initial stock',
        ]);
    }
}
