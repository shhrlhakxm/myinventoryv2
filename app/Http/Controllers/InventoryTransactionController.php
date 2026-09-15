<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use Illuminate\View\View;

class InventoryTransactionController extends Controller
{
    public function index(): View
    {
        $transactions = InventoryTransaction::query()
            ->with(['item', 'user'])
            ->latest()
            ->paginate(15);

        return view('inventory-transactions.index', compact('transactions'));
    }
}
