<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;
    public function definition()
    {
        return [
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'reason' => $this->faker->text(10),
            'debit_or_credit' => $this->faker->randomElement(['debit', 'credit']),
            'user_id' => 1
        ];
    }
}
