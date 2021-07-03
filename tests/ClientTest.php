<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ClientTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testList()
    {
        $this->get('/clients');

        $this->assertEquals(
            401,
            $this->response->status()
        );
    }
}
