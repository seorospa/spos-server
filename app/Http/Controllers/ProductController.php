<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMoviment;

class ProductController extends Controller
{
    public function list()
    {
        $visible =  $this->request->input('visible', ['id', 'title', 'code', 'price']);
        $params = $this->request->all();
        $code = $this->request->code;

        $limit = $this->request->input('limit', 100);

        $products = $code ?
            Product::where('code', $code)->firstOrFail() :
            Product::filter($params)->simplePaginate($limit, $visible);

        return response()->json($products);
    }

    public function create_bulk()
    {
        $arr = $this->request->all();
        $ids = [];

        foreach ($arr as $params) {
            $product = Product::create($params);
            $ids[] = $product->id;
        }

        return response()->json(['ids_range' => [min($ids), max($ids)]], 201);
    }

    public function create()
    {
        $this->validate(
            $this->request,
            [
                'title' => 'required',
                'code' => 'required',
                'price' => 'required',
            ]
        );

        $this->validate_props();

        $product = Product::create($this->request->all());

        return response()->json($product);
    }

    public function read($id)
    {
        $ticket = Product::findOrFail($id);
        return response()->json($ticket);
    }

    public function update($id)
    {
        $this->validate_props();

        $params = $this->request->all();
        $product = Product::findOrFail($id)->update($params);
        $product->update($params);

        return response()->json($product);
    }

    public function delete($id)
    {
        Product::findOrFail($id)->delete();

        return response('');
    }

    public function stock($id)
    {
        $this->validate(
            $this->request,
            ['qty' => 'required|numeric']
        );

        $qty = $this->request->input('qty');
        $product = Product::findOrFail($id);

        $qty = max($qty + $product->qty, 0);

        $diff = $qty - $product->qty;

        if ($diff != 0)
            $this->stock_moviment($product->id, $diff);

        $product->update(['qty' => $qty]);

        return response('');
    }

    public function stock_moviment($id, $qty)
    {
        $user = $this->logged->id;

        $moviment = StockMoviment::create([
            'product' => $id,
            'qty' => $qty,
            'user' => $user
        ]);

        return $moviment;
    }

    public function validate_props()
    {
        return $this->validate($this->request, [
            'title' => 'max:63',
            'code' => 'alpha_dash|max:127',
            'price' => 'numeric|min:0',
            'qty' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'min' => 'nullable|integer|min:0',
            'max' => 'nullable|integer|min:0',
            'ws_min' => 'nullable|integer|min:0',
            'ws_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|integer|exists:categories,id',
            'unit' => 'nullable|boolean',
            'taxes' => 'nullable|string',
        ]);
    }
}
