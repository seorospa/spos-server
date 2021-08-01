<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function list()
    {
        $visible =  $this->request->input('visible', ['id', 'name', 'user', 'status']);
        $params = $this->request->all();

        $tickets = Ticket::filter($params)->get($visible);
        return response()->json($tickets);
    }

    public function create()
    {
        $this->validate(
            $this->request,
            [
                'name' => 'required|max:15',
            ]
        );

        $ticket = Ticket::firstOrNew(['status' => 'deleted']);

        $ticket->user = $this->logged->id;
        $ticket->name = $this->request->input('name');
        $ticket->status = 'pending';

        $ticket->save();

        return response()->json($ticket->only(['id', 'name']));
    }

    public function read($id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket);
    }

    public function update($id)
    {
        $this->validate(
            $this->request,
            [
                'status' => 'different:deleted',
                'name' => 'max:15',
                'client' => 'exists:clients,id'
            ]
        );

        $params = $this->request->all();

        if ($params['status'] == 'deleted')
            return response()->json(['status' => 'The status field should be different from deleted'], 422);

        Ticket::findOrFail($id)->update($params);

        return response('');
    }

    public function delete($id)
    {
        $ticket = Ticket::findOrFail($id);

        $codes = array_keys($ticket->products);

        foreach ($codes as $code)
            $this->deleteProductFromTicket($ticket, $code);

        $ticket->status = 'deleted';

        $ticket->save();

        return response('');
    }

    /**
     * Actions
     */


    public function changeProductQty($id)
    {
        $this->validate(
            $this->request,
            [
                'qty' => 'required|numeric|gt:0',
                'code' => 'required|exists:products,code',
            ]
        );

        $visible = ['title', 'price', 'qty', 'unit'];
        $code = $this->request->input('code');
        $qty = $this->request->input('qty');

        $ticket = Ticket::findOrFail($id);
        $product = Product::where('code', $code)->first();

        $valid = $product->qty ?? 1;

        if ($ticket->status != 'pending' || $valid == 0)
            return response('', 404);

        $p = $ticket->products;

        if (isset($product->qty)) {
            $product->qty += $p[$code]['qty'] ?? 0;
            
            $qty = min($qty, $product->qty);

            $product->qty -= $qty;
            $product->save();
        }

        $reg = $product->only($visible);

        $reg['stock'] = $reg['qty'];
        $reg['qty'] = $qty;

        $p[$code] = $reg;

        $ticket->products = $p;
        $ticket->save();

        return response()->json($p);
    }

    public function deleteProduct($id)
    {
        $this->validate(
            $this->request,
            [
                'code' => 'required|exists:products,code'
            ]
        );

        $code = $this->request->input('code');
        $ticket = Ticket::findOrFail($id);

        $this->deleteProductFromTicket($ticket, $code);

        $ticket->save();

        return response('');
    }

    public function deleteProductFromTicket($ticket, $code)
    {
        $product = Product::where('code', $code)->firstOrFail();
        $p = $ticket->products;

        if (isset($product->qty)) {
            $product->qty += $p['code']['qty'] ?? 0;
            $product->save();
        }

        unset($p[$code]);

        $ticket->products = $p;
    }

    public function claim($id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update(['user' => $this->logged->id]);

        return response('');
    }
}
