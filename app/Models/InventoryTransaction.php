<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TransactionType;
use Database\Factories\InventoryTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTransaction extends Model
{

    use HasFactory;
    protected $fillable = ['item_id', 'user_id', 'type', 'quantity', 'notes'];

    protected function casts(): array
    {
        return [
            'type' => \App\Enums\TransactionType::class, // see note below
            'quantity' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
