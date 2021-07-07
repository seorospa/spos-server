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
            'father' => 'integer|exists:categories,id'
        ]);

        $category = Category::create($this->request->all());

        return response()->json($category);
    }

    public function read($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update($id)
    {
        $this->validate($this->request, [
            'name' => 'required|max:31',
            'father' => 'integer|exists:categories,id'
        ]);

        $category = Category::findOrFail($id);
        $category->update($this->request->all());

        return response()->json($category);
    }

    public function delete($id)
    {
        Category::findOrFail($id)->delete();
        return response('');
    }
}
