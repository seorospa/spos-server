<?php

use App\Models\Product;
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
        $this->seeStatusCode(200);
    }

    public function testRead()
    {
        $user = User::find(1);
        $products = Product::get();

        foreach ($products as $product)
        {
            $id = $product->id;
            $this->actingAs($user)->json('GET', "/products/$id")->seeJsonEquals($product->toArray());
        }
    }
}
