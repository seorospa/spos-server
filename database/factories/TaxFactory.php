<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'percentage' => $this->faker->randomFloat(2, 0.01, 1),
            'fixed' => $this->faker->randomFloat(2, 1, 1000),
        ];
    }
}
