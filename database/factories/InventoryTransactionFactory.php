<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryTransaction>
 */
class InventoryTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'user_id' => User::factory(),
            'type' => fake()->randomElement(TransactionType::cases()),
            'quantity' => fake()->numberBetween(1, 100),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
