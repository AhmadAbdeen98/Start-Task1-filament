<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $costPrice = fake()->randomFloat(2, 5, 500);
        $price = $costPrice * fake()->randomFloat(2, 1.2, 2.5);

        return [
            'category_id' => Category::factory(),
            'name' => ucfirst(fake()->unique()->words(3, true)),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-????')),
            'description' => fake()->paragraph(3),
            'price' => round($price, 2),
            'cost_price' => round($costPrice, 2),
            'stock_quantity' => fake()->numberBetween(0, 500),
            'image_path' => null,
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive']),
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
