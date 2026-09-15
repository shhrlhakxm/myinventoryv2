<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Item;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalItems = Item::count();
        $lowStockItems = Item::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $recentTransactions = InventoryTransaction::query()
            ->with('item', 'user')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('totalItems', 'lowStockItems', 'recentTransactions'));
    }
}
