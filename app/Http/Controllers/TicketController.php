<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TicketController extends CruldController
{
    protected $model = Ticket::class;

    protected $gate_edit = 'edit-ticket';

    public function claim(int $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update(['user_id' => Auth::user()->id]);

        return response('');
    }

    protected function preDelete($ticket)
    {
        $codes = array_keys($ticket->products);

        foreach ($codes as $code)
            $this->deleteProductFromTicket($ticket, $code);
    }

    public function changeProductQty(Request $request, int $id)
    {
        $this->validate(
            $request,
            [
                'qty' => 'required|numeric|gt:0',
                'code' => 'required|exists:products,code',
            ]
        );

        $ticket = Ticket::findOrFail($id);

        if (Gate::denies('ticket-product', $ticket))
            abort(403);

        $code = $request->code;
        $qty = $request->qty;

        $product = Product::where('code', $code)->first();

        $cart = $ticket->cart;

        if (isset($product->qty)) {
            $product->qty += $cart[$code]['qty'] ?? 0;
            $qty = min($qty, $product->qty);

            $product->qty -= $qty;

            $product->save();
        }

        $reg = $product->only(['title', 'price', 'qty', 'unit']);

        $reg['stock'] = $reg['qty'];
        $reg['qty'] = $qty;

        $cart[$code] = $reg;

        $ticket->cart = $cart;
        $ticket->save();

        return response()->json($cart);
    }

    public function deleteProduct(Request $request, int $id)
    {
        $this->validate(
            $request,
            [
                'code' => 'required|exists:products,code'
            ]
        );

        $ticket = Ticket::findOrFail($id);

        if (Gate::denies('ticket-product', $ticket))
            abort(401);


        $this->deleteProductFromTicket($ticket, $request->code);

        $ticket->save();

        return response('');
    }

    protected function deleteProductFromTicket(Ticket $ticket, string $code)
    {
        $product = Product::where('code', $code)->firstOrFail();

        if (Gate::denies('ticket-product', $ticket))
            abort(401);

        $cart = $ticket->cart;

        if (isset($product->qty)) {
            $product->qty += $cart['code']['qty'] ?? 0;
            $product->save();
        }

        unset($cart[$code]);

        $ticket->cart = $cart;
    }
}
