<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'title' => $this->faker->name,
            'code' => $this->faker->ean8,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'qty' => $this->faker->numberBetween(1, 100),
            'cost' => function (array $attributes) {
              return $this->faker->randomFloat(2, 1, $attributes['price']);
            },
            'min' => $this->faker->numberBetween(1, 100),
            'max' => function (array $attributes) {
              return $this->faker->numberBetween($attributes['min'], 100);
            },
            'ws_min' => $this->faker->numberBetween(1, 100),
            'ws_max' => function (array $attributes) {
              return $this->faker->numberBetween($attributes['ws_min'], 100);
            },
            'category' => Category::all()->random()->id,
            'unit' => '1',
            'taxes' => null,
        ];
    }
}
