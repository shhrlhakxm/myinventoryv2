<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::updateOrCreate(
            ['code' => 'P01'],
            [
                'name' => 'Packaging',
                'description' => 'Packaging materials',
            ],
        );

        Category::updateOrCreate(
            ['code' => 'CB01'],
            [
                'name' => 'Coffee and Beverages',
                'description' => 'Coffee and beverages',
            ],
        );
    }
}
