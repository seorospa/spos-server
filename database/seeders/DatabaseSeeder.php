<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
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
        User::create([
            'name' => 'admin',
            'password' => 'password',
            'is_admin' => 1
        ]);

        Category::create([
            'name' => 'common_product',
        ]);

        if (env('APP_DEBUG', true)) {
            User::factory()->count(30)->create();
            Client::factory()->count(30)->create();
            Category::factory()->count(30)->create();
            Product::factory()->count(30)->create();
            Ticket::factory()->count(30)->create();
            Transaction::factory()->count(30)->create();
        }
    }
}
