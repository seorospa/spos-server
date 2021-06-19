<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $this->post('/auth', ['id' => 1, 'password' => '12345']);

        $this->assertEquals(
            200,
            $this->response->status()
        );
    }

    public function testRefresh()
    {
        $user = User::find(1);

        $this->actingAs($user)->get('/auth');

        $this->assertEquals(
            200,
            $this->response->status()
        );
    }
}
