<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ticket;
use App\Models\User;

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
        $id_user = $this->logged->id;
        $user = User::findOrFail($id_user);

        $ticket = Ticket::where('status', 'deleted')->first() ?? Ticket::create(
            [
                'user' => $id_user,
                'name' => $user,
                'status' => 'pending'
            ]
        );

        $ticket->status = 'pending';
        $ticket->name = $user->name;
        $ticket->user = $id_user;

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
        $state = Ticket::findOrFail($id);

        if ($state->status != "pending") {
            return response('', 404);
        } else {

            $params = $this->request->all();
            Ticket::findOrFail($id)->update($params);

            return response();
        }
    }

    public function delete($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->status != 'pending') {
            return response('', 404);
        }

        $ticket->update(['status' => 'deleted']);

        $code = array_keys($ticket['products']);

        foreach ($ticket['products'] as $code => $pr) {
            $amount[] = $pr['qty'];

            $model = Product::where('code', $code)->first();

            $p[] = $model->qty;
        }

        $code = array_keys($ticket['products']);

        $ticket = count($ticket['products']);

        for ($i = 0; $i < $ticket; $i++) {

            $back[] = $p[$i] + $amount[$i];

            Product::where('code', $code[$i])->update(['qty' => $back[$i]]);
        }

        return response('');
    }

    /**
     * Product
     */

    public function addProducts($id)
    {
        $this->validate(
            $this->request,
            [
                'amount' => 'required|numeric',
                'code' => 'required|exists:products,code'
            ]
        );

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status != 'pending') {
            return response('', 404);
        }

        $products = $ticket->products;

        $code = $this->request->input('code');
        $amount = $this->request->input('amount');

        $visible = ['title', 'price', 'qty', 'unit'];

        $p = Product::where('code', $code)->first();

        $curr = $p->qty - $amount;
        $amount += $products[$code]['amount'] ?? 0;

        if ($curr < 0 || $amount < 0)
            return response()->json(['msg' => 'invalid amount'], 405);
        elseif ($amount == 0)
            return $this->deleteProduct($id);

        $p->qty = $curr;

        $reg = $p->only($visible);

        //$reg['stock'] = $reg['qty'];
        //$reg['amount'] = $amount;

        $reg['amount'] = $amount;

        $products[$code] = $reg;
        $ticket->products = $products;

        $ticket->save();
        $p->save();

        return response()->json($products);
    }

    public function deleteProduct($id)
    {
        $code = $this->request->input('code');

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status != 'pending') {
            return response('', 404);
        }

        $reg = $ticket->products;

        $p = Product::where('code', $code)->first();

        if (isset($reg[$code])) {
            $back = $p->qty + $reg[$code]['amount'];
            $p->update(['qty' => $back]);
        }

        unset($reg[$code]);

        $ticket->update(['products' => $reg]);

        return response('');
    }

    public function changeProduct($id)
    {
        $this->validate(
            $this->request,
            [
                'amount' => 'required|numeric',
                'code' => 'required|exists:products,code'
            ]
        );

        $code = $this->request->input('code');
        $amount = $this->request->input('amount');

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status != 'pending') {
            return response('', 404);
        }

        $reg = $ticket->products;

        $p = Product::where('code', $code)->first();

        $diff = $reg[$code]['amount'] - $amount;

        $new_qty = $p->qty + $diff;

        $p->update(['qty' => $new_qty]);

        $reg[$code]['amount'] = $amount;
        $reg[$code]['qty'] = $new_qty;

        if ($reg[$code]['amount'] <= 0) {
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
