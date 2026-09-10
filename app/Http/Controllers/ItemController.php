<?php
// app/Http/Controllers/ItemController.php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Services\StockMovementService;

class ItemController extends Controller
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    public function index()
    {
        $items = Item::with('category')
            ->orderBy('name')
            ->paginate(15);

        return view('items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('items.create', compact('categories'));
    }

    public function store(StoreItemRequest $request)
    {
        $validated = $request->validated();
        $initialStock = $validated['current_stock'];
        unset($validated['current_stock']);

        $item = Item::create([...$validated, 'current_stock' => 0]);

        if ($initialStock > 0) {
            $this->stockMovementService->record(
                item: $item,
                user: $request->user(),
                type: TransactionType::In,
                quantity: $initialStock,
                notes: 'Initial stock on item creation',
            );
        }

        return redirect()
            ->route('items.index')
            ->with('status', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();

        return view('items.edit', compact('item', 'categories'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        return redirect()
            ->route('items.index')
            ->with('status', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        if ($item->inventoryTransactions()->exists()) {
            return redirect()
                ->route('items.index')
                ->with('error', 'Cannot delete an item that has recorded transactions.');
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('status', 'Item deleted.');
    }
}