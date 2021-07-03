<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function list()
    {
        $visible = ['id', 'name'];
        $tickets = Category::all($visible);
        return response()->json($tickets);
    }

    public function create()
    {
        $this->validate($this->request, [
            'name' => 'required|max:31',
            'father' => 'integer'
        ]);

        $category = Category::create($this->request->all());

        return response()->json($category);
    }

    public function read($id)
    {
        $ticket = Category::findOrFail($id);
        return response()->json($ticket);
    }

    public function update($id)
    {
        $params = $this->request->all();
        Category::findOrFail($id)->update($params);

        return response('');
    }

    public function delete($id)
    {
        Category::findOrFail($id)->delete();
        return response('');
    }
}
