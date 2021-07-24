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
        $tickets = Ticket::get();

        foreach ($tickets as $ticket)
        {
            $id = $ticket->id;
            $this->actingAs($user)->json('GET', "/tickets/$id")->seeJsonEquals($ticket->toArray());
        }
    }

    public function testNotFound()
    {
        $user = User::find(1);
        $invalid_ids = [-1, 0, 100000000];

        foreach ($invalid_ids as $id)
        {
            $this->actingAs($user)->json('GET', "/tickets/$id");
            $this->seeStatusCode(404);
        }
    }
}
