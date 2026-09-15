<?php

namespace Tests\Feature\StockMovement;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_authenticated_user_can_record_a_signed_stock_adjustment(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();
        $item = Item::factory()->create(
            [
                'category_id' => $category->id,
                'current_stock' => 10,
            ]
        );

        $response = $this->actingAs($user)->post(route('stock-movements.store', $item), [
            'type' => TransactionType::Adjustment->value,
            'quantity' => -3,
            'notes' => 'Stock adjustment',
        ]);

        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('status', 'Stock Adjustment recorded successfully.');
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'current_stock' => 7,
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => TransactionType::Adjustment->value,
            'quantity' => -3,
            'notes' => 'Stock adjustment',
        ]);
    }

    public function test_stock_out_is_rejected_when_quantity_exceeds_current_stock(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();
        $item = Item::factory()->create(
            [
                'category_id' => $category->id,
                'name' => 'Espresso Beans',
                'current_stock' => 5,
            ]
        );

        $formUrl = route(
            'stock-movements.create',
            [
                'item' => $item->id,
                'type' => TransactionType::Out->value,
            ]
        );

        $response = $this->actingAs($user)
            ->from($formUrl)
            ->post(route('stock-movements.store', $item), [
                'type' => TransactionType::Out->value,
                'quantity' => 10,
                'notes' => 'Attempt to remove more stock than available',
            ]);

        $response->assertSessionHas(
            'error',
            'Cannot remove 10 units of "Espresso Beans": only 5 in stock.',
        );
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'current_stock' => 5,
        ]);
        $response->assertRedirect($formUrl);
        $this->assertDatabaseMissing('inventory_transactions', [
            'item_id' => $item->id,
        ]);
    }
}
