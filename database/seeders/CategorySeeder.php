<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Category::insert([
            ['name' => 'Packaging','code' => 'P01', 'description' => 'Packaging materials'],
            ['name'=> 'Coffee and Beverages','code' => 'CB01', 'description' => 'Coffee and beverages']
        ]);
    }
}
