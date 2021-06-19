<?php

use App\Models\Ticket;
use App\Models\User;

class TicketTest extends TestCase
{
    public function testList()
    {
        $user = User::find(1);

        $tickets = Ticket::all(['id', 'name', 'status', 'user'])->toArray();
        $this->actingAs($user)->json('GET', '/tickets')->seeJsonEquals($tickets);
    }

    public function testRead()
    {
        $user = User::find(1);

        for ($i = 1; $i < 4; $i++) {
            $ticket = Ticket::find($i)->toArray();
            $this->actingAs($user)->json('GET', "/tickets/$i")->seeJsonEquals($ticket);
        }
    }

    public function testNotFound()
    {
        $user = User::find(1);

        $response1 = $this->call('GET', '/tickets/-1');
        $response2 = $this->call('GET', '/tickets/1000000000');

        $this->actingAs($user)->assertEquals(401, $response1->status());
        $this->actingAs($user)->assertEquals(401, $response2->status());
    }
}
