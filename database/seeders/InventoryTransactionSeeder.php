<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventoryTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()
            ->where('email', 'admin@test.com')
            ->firstOrFail();

        $staff = User::query()
            ->where('email', 'user@test.com')
            ->firstOrFail();

        $transactions = [
            [
                'sku' => 'PKG-CUP-12',
                'user_id' => $admin->id,
                'type' => TransactionType::In,
                'quantity' => 150,
                'notes' => 'Initial demo stock for paper cups',
            ],
            [
                'sku' => 'PKG-CUP-12',
                'user_id' => $staff->id,
                'type' => TransactionType::Out,
                'quantity' => 30,
                'notes' => 'Demo order usage for paper cups',
            ],
            [
                'sku' => 'PKG-LID-12',
                'user_id' => $admin->id,
                'type' => TransactionType::In,
                'quantity' => 30,
                'notes' => 'Initial demo stock for cup lids',
            ],
            [
                'sku' => 'BEV-COFFEE-1KG',
                'user_id' => $admin->id,
                'type' => TransactionType::In,
                'quantity' => 20,
                'notes' => 'Initial demo stock for coffee beans',
            ],
            [
                'sku' => 'BEV-COFFEE-1KG',
                'user_id' => $staff->id,
                'type' => TransactionType::Out,
                'quantity' => 2,
                'notes' => 'Demo coffee bean usage',
            ],
            [
                'sku' => 'BEV-MILK-1L',
                'user_id' => $admin->id,
                'type' => TransactionType::In,
                'quantity' => 12,
                'notes' => 'Initial demo stock for milk',
            ],
            [
                'sku' => 'BEV-MILK-1L',
                'user_id' => $staff->id,
                'type' => TransactionType::Out,
                'quantity' => 4,
                'notes' => 'Demo milk usage',
            ],
        ];

        foreach ($transactions as $transaction) {
            $item = Item::query()
                ->where('sku', $transaction['sku'])
                ->firstOrFail();

            InventoryTransaction::firstOrCreate(
                [
                    'item_id' => $item->id,
                    'notes' => $transaction['notes'],
                ],
                [
                    'user_id' => $transaction['user_id'],
                    'type' => $transaction['type'],
                    'quantity' => $transaction['quantity'],
                ],
            );
        }
    }
}
