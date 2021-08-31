<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ticket;
use App\Models\Category;
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
        $codes = array_keys($ticket->cart);

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

        // if (Gate::denies('ticket-product', $ticket)) //TODO: DELETE THIS
        //     abort(403);

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
        $this->validate($request, ['code' => 'required|max:31']);

        $ticket = Ticket::findOrFail($id);

        if (!key_exists($request->code, $ticket->cart) || Gate::denies('ticket-product', $ticket))
            abort(401);

        $this->deleteProductFromTicket($ticket, $request->code);

        $ticket->save();

        return response()->json($ticket);
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

    public function changeCommonProduct(Request $request, int $id)
    {
        $this->validate(
            $request,
            [
                'title' => 'required|max:31',
                'price' => 'required|numeric',
                'qty' => 'required|numeric',
                'code' => 'max:31',
            ]
        );

        $ticket = Ticket::findOrFail($id);
        $cart = $ticket->cart;
        $title = $request->title;
        $price = $request->price;
        $qty = $request->qty;

        $code = $request->code ?? $this->generateRandomCode($ticket);

        $commonProductCategory = Category::where('name', 'common_product')->first();

        $cart[$code] = [
            'title' => $title,
            'price' => $price,
            'category' => $commonProductCategory->id,
            'qty' => $qty,
            'code' => $code,
            'is_common' => true,
        ];

        $ticket->cart = $cart;
        $ticket->save();

        return response()->json($ticket);
    }

    private function generateRandomCode(array $cart, $length = 20)
    {
        $code = '';

        do {
            $code = '~' . substr(base64_encode(mt_rand()), 0, $length);
        } while (key_exists($code, $cart));

        return $code;
    }
}
