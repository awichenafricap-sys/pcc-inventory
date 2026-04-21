<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'sku' => 'ING-' . fake()->unique()->numerify('######'),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id ?? 1,
            'unit_of_measurement' => fake()->randomElement(['kg', 'liters', 'pieces', 'grams', 'ml', 'boxes']),
            'current_stock' => fake()->randomFloat(2, 0, 1000),
            'minimum_stock' => fake()->randomFloat(2, 10, 100),
            'cost_per_unit' => fake()->randomFloat(2, 1, 500),
            'supplier' => fake()->company(),
            'location' => fake()->randomElement(['Warehouse A', 'Warehouse B', 'Storage Room 1', 'Storage Room 2']),
            'expiry_date' => fake()->dateTimeBetween('+1 week', '+2 years'),
            'description' => fake()->sentence(10),
        ];
    }
}
