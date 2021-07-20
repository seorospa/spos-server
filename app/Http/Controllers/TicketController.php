<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function list()
    {
        $visible =  $this->request->input('visible', ['id', 'name', 'user', 'status']);
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

    /**
     * Product
     */

    public function addProducts($id)
    {
        $ticket = Ticket::findOrFail($id);

        $code = $this->request->input('code');
        $qty = $this->request->input('qty');

        $p = Product::where('code', $code)->firstOrFail();

        $curr = $p->qty - $qty;

        if ($curr <= 0) {
            return response('', 405);
        }

        $p->update(['qty' => $curr]);

        $reg = json_decode($ticket->products, true);

        $qty = empty($reg[$code]) ? $qty : $qty + $reg[$code]['qty'];

        $reg[$code] = [
            'title' => $p['title'],
            'price' => $p['price'],
            'category' => $p['category'],
            'qty' => $qty
        ];

        $ticket->products = $reg;
        $ticket->save();

        return response()->json($reg);
    }

    public function deleteProduct($id)
    {
        $code = $this->request->input('code');

        $ticket = Ticket::findOrFail($id);

        $reg = json_decode($ticket->products, true);

        unset($reg[$code]);

        $ticket->update(['products' => $reg]);

        return response('');
    }

    public function changeProduct($id)
    {
        $code = $this->request->input('code');
        $qty = $this->request->input('qty');

        $ticket = Ticket::findOrFail($id);

        $reg = json_decode($ticket->products, true);

        $reg[$code]['qty'] = $qty;

        if ($reg[$code]['qty'] <= 0) {
            unset($reg[$code]);
        }

        $ticket->update(['products' => $reg]);

        return response('');
    }

    public function claim($id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update(['user' => $this->logged->id]);

        return response('');
    }
}
