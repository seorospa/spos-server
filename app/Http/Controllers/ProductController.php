<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function list()
    {
        $visible = ['id', 'title', 'code', 'price'];
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
                'price' => 'required'
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
        Product::findOrFail($id)->update($params);

        return response('');
    }

    public function delete($id)
    {
        Product::findOrFail($id)->delete();

        return response('');
    }

    public function validate_props()
    {
        return $this->validate($this->request, [
            'title' => 'max:63',
            'code' => 'alpha_dash|max:127',
            'price' => 'numeric',
            'qty' => 'numeric',
            'cost' => 'numeric',
            'min' => 'numeric',
            'max' => 'numeric',
            'ws_min' => 'numeric',
            'ws_price' => 'numeric',
            'category' => 'integer'
        ]);
    }
}
