<?php

namespace Database\Seeders;

use App\Models\User;
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
            $users_num = 15;
            User::factory()->count($users_num)->create();
        } else {
            User::create([
                'name' => 'admin',
                'password' => '$2y$10$eVlqUoJWL22Kv8xlUTM8HeGqQqw4P06Qbjo6xP8uwnxH6FreBiDIy',
                'is_admin' => 1
            ]);
        }
    }
}
