<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Exceptions\InsufficientStockException;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    /**
     * Record a stock movement and keep Item::current_stock in sync,
     * atomically. Throws if the movement would push stock below zero.
     */
    public function record(
        Item $item,
        User $user,
        TransactionType $type,
        int $quantity,
        ?string $notes = null,
    ): InventoryTransaction {
        return DB::transaction(function () use ($item, $user, $type, $quantity, $notes) {
            // Lock the row so two concurrent requests can't both read
            // the same current_stock before either one writes.
            $item = Item::whereKey($item->id)->lockForUpdate()->firstOrFail();

            $delta = match ($type) {
                TransactionType::In => $quantity,
                TransactionType::Out => -$quantity,
                TransactionType::Adjustment => $quantity, // caller passes signed value
            };

            $newStock = $item->current_stock + $delta;

            if ($newStock < 0) {
                throw new InsufficientStockException($item->name, $item->current_stock, abs($delta));
            }

            $item->update(['current_stock' => $newStock]);

            return $item->inventoryTransactions()->create([
                'user_id' => $user->id,
                'type' => $type,
                'quantity' => $quantity,
                'notes' => $notes,
            ]);
        });
    }
}