<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testList()
    {
        $user = User::find(1);

        $this->actingAs($user)->get('/products');

        $this->assertEquals(
            200, $this->response->status()
        );
    }
}
