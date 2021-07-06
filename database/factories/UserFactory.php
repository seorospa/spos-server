<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'password' => '$2y$10$eVlqUoJWL22Kv8xlUTM8HeGqQqw4P06Qbjo6xP8uwnxH6FreBiDIy',
            'email' => $this->faker->safeEmail,
            'is_admin' => false,
            'permissions' => '',
        ];
    }
}
