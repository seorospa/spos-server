<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_DEBUG', true)) {
            User::factory()->count(30)->create();
            Client::factory()->count(30)->create();
            Category::factory()->count(30)->create();
            Product::factory()->count(30)->create();
            Ticket::factory()->count(30)->create();
        } else {
            User::create([
                'name' => 'admin',
                'password' => '$2y$10$eVlqUoJWL22Kv8xlUTM8HeGqQqw4P06Qbjo6xP8uwnxH6FreBiDIy',
                'is_admin' => 1
            ]);
        }
    }
}
