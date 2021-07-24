<?php

use App\Models\User;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $this->post('/auth/1', ['password' => 'password']);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(['token']);
    }

    public function testRefresh()
    {
        $user = User::find(1);

        $this->actingAs($user)->get('/auth');
        $this->seeStatusCode(200);
        $this->seeJsonStructure(['token']);
    }
}
