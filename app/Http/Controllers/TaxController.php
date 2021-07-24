<?php

namespace App\Http\Controllers;

use App\Models\Tax;

class TaxController extends Controller
{
    public function list()
    {
        $params = $this->request->all();
        $limit = $this->request->input('limit', 100);
        $visible =  $this->request->input('visible', ['id', 'name']);

        $taxes = Tax::filter($params)->simplePaginate($limit, $visible);;
        return response()->json($taxes);
    }

    public function create()
    {
        $this->validate(
            $this->request,
            [
                'name' => 'required|max:31',
                'percentage' => 'nullable|numeric',
                'fixed' => 'nullable|numeric'
            ]
        );

        $tax = Tax::create($this->request->all());

        return response()->json($tax);
    }

    public function read($id)
    {
        $tax = Tax::findOrFail($id);
        return response()->json($tax);
    }

    public function update($id)
    {
        $params = $this->request->all();
        $tax = Tax::findOrFail($id);
        $tax->update($params);

        return response()->json($tax);
    }

    public function delete($id)
    {
        Tax::findOrFail($id)->delete();

        return response('');
    }
}
