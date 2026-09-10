<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Enums\TransactionType;
use App\Http\Requests\StockMovement\StoreStockMovementRequest;
use App\Services\StockMovementService;
use App\Exceptions\InsufficientStockException;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected StockMovementService $stockMovementService) {}

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Item $item, string $type)
    {
        $transactionType = TransactionType::from($type);

        return view('stock-movements.create', compact('item', 'transactionType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockMovementRequest $request, Item $item)
    {
        $validated = $request->validated();
        $type = TransactionType::from($validated['type']);

        try {
            $this->stockMovementService->record(
                item: $item,
                user: $request->user(),
                type: $type,
                quantity: $validated['quantity'],
                notes: $validated['notes'] ?? null,
            );
        } catch (InsufficientStockException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('items.index')
            ->with('status', "Stock {$type->label()} recorded successfully.");
    }
        //

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
    }
}
