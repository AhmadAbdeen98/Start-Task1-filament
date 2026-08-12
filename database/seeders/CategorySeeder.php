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
        $named = [
            ['name' => 'Electronics', 'description' => 'Phones, laptops, and electronic accessories.', 'status' => 'active'],
            ['name' => 'Home & Kitchen', 'description' => 'Appliances and everyday kitchen essentials.', 'status' => 'active'],
            ['name' => 'Clothing & Apparel', 'description' => 'Men\'s and women\'s clothing.', 'status' => 'active'],
            ['name' => 'Sports & Outdoors', 'description' => 'Gear and equipment for outdoor activities.', 'status' => 'active'],
            ['name' => 'Discontinued Lines', 'description' => 'Legacy category kept for historical records.', 'status' => 'inactive'],
        ];

        foreach ($named as $category) {
            Category::create($category);
        }

        // Additional randomized categories for a fuller demo dataset.
        Category::factory()->count(5)->create();
    }
}
