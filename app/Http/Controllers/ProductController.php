<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends CruldController
{
    protected $model = Product::class;
    
    public function list(Request $request)
    {
        $code = $request->code ?? null;

        return $code ?
            Product::where('code', $code)->firstOrFail() :
            parent::list($request);
    }

    public function stock(Request $request, int $id)
    {
        $this->validate(
            $request,
            [
                'qty' => 'required|numeric'
            ]
        );

        $product = Product::findOrFail($id);
        $qty = $request->qty;

        $qty = max($qty + $product->qty, 0);

        $diff = $qty - $product->qty;

        if ($diff != 0)
            $this->movement($product->id, $diff);

        $product->update(['qty' => $qty]);

        return response('');
    }

    public function movement(int $id, float $qty)
    {
        $user_id = Auth::user()->id;

        Movement::create([
            'user_id' => $user_id,
            'product' => $id,
            'qty' => $qty,
        ]);
    }
}
