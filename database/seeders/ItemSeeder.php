<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packaging = Category::query()
            ->where('code', 'P01')
            ->firstOrFail();

        $beverages = Category::query()
            ->where('code', 'CB01')
            ->firstOrFail();

        Item::firstOrCreate(
            ['sku' => 'PKG-CUP-12'],
            [
                'category_id' => $packaging->id,
                'name' => 'Paper Cups 12 oz',
                'current_stock' => 120,
                'minimum_stock' => 40,
                'unit_price' => 0.18,
            ],
        );

        Item::firstOrCreate(
            ['sku' => 'PKG-LID-12'],
            [
                'category_id' => $packaging->id,
                'name' => 'Cup Lids 12 oz',
                'current_stock' => 30,
                'minimum_stock' => 40,
                'unit_price' => 0.12,
            ],
        );

        Item::firstOrCreate(
            ['sku' => 'BEV-COFFEE-1KG'],
            [
                'category_id' => $beverages->id,
                'name' => 'Arabica Coffee Beans 1 kg',
                'current_stock' => 18,
                'minimum_stock' => 10,
                'unit_price' => 74.90,
            ],
        );

        Item::firstOrCreate(
            ['sku' => 'BEV-MILK-1L'],
            [
                'category_id' => $beverages->id,
                'name' => 'UHT Milk 1 L',
                'current_stock' => 8,
                'minimum_stock' => 12,
                'unit_price' => 6.50,
            ],
        );
    }
}
