<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    private function generateFakeProducts() {
      return '[]'; // TODO: ¿Cómo se verá la lista de productos?
    }

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'user' => User::all()->random()->id,
            'products' => $this->generateFakeProducts(),
            'client' => Client::all()->random()->id,
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
        ];
    }
}
