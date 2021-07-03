<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class TicketController extends Controller
{
    public function list()
    {
        $visible = ['id', 'name', 'user', 'status'];
        $tickets = Ticket::all($visible);
        return response()->json($tickets);
    }

    public function create()
    {
        $this->validate(
            $this->request,
            [
                'user' => 'required|integer',
                'name' => 'required|max:255',
                'state' => 'prohibited',
                'client' => 'prohibited',
                'status' => 'prohibited'
            ],
        );

        $params = $this->request->all();
        $ticket = Ticket::create($params);

        return response()->json($ticket->only(['id', 'name']));
    }

    public function read($id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket);
    }

    public function update($id)
    {
        $params = $this->request->all();
        Ticket::findOrFail($id)->update($params);

        return response('');
    }

    public function delete($id)
    {
        Ticket::findOrFail($id)->delete();
        return response('');
    }
}
