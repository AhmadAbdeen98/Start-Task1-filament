<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        // Ensure every category has a handful of products.
        $categories->each(function (Category $category) {
            Product::factory()
                ->count(random_int(4, 8))
                ->create(['category_id' => $category->id]);
        });

        // A few explicit edge-case products, useful for demoing filters/search.
        Product::factory()->create([
            'category_id' => $categories->first()->id,
            'name' => 'Wireless Bluetooth Headphones',
            'sku' => 'SKU-DEMO-0001',
            'price' => 89.99,
            'cost_price' => 42.50,
            'stock_quantity' => 0,
            'status' => 'active',
        ]);

        Product::factory()->inactive()->create([
            'category_id' => $categories->last()->id,
            'name' => 'Legacy Desktop Fan',
            'sku' => 'SKU-DEMO-0002',
        ]);
    }
}
