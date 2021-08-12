<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Laravel\Lumen\Routing\Controller;

class CruldController extends Controller
{
    protected $model;

    protected $gate_edit = null;
    protected $gate_create = null;
    
    public function __construct(Request $req)
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $listed = $request->listed ?? $this->model::$listed;
        $limit = min($request->limit ?? 20, 100);

        $filtred = $this->model::filter($request->all());
        
        $models = $filtred->simplePaginate($limit, $listed);

        return $this->success($models);
    }

    public function create(Request $request)
    {
        if ($this->gate_create && Gate::denies($this->gate_create, $this->model))
            abort(403);

        $rules = $this->model::$rules;

        foreach ($this->model::$required as $attr)   
            $rules[$attr] .= '|required';

        $this->validate($request, $rules);

        $model = new $this->model($request->all());

        if (Gate::allows('user_id', $model))
            $model->user_id = Auth::user()->id;
            
        $model->save();

        $res = $model->only('id');

        return $this->success($res);
    }

    public function read(int $id)
    {
        $model = $this->model::findOrFail($id);
        return response()->json($model);
    }

    public function update(Request $request, int $id)
    {
        $rules = $this->model::$rules;

        $this->validate($request, $rules);
        
        $params = $request->all();
        $model = $this->model::findOrFail($id);

        if ($this->gate_edit && Gate::denies($this->gate_edit, $model))
            abort(403);

        $model->update($params);

        return response()->json($model);
    }

    public function delete(int $id)
    {
        $model = $this->model::findOrFail($id);
        
        if ($this->gate_edit && Gate::denies($this->gate_edit, $model))
            abort(403);

        $this->preDelete($model);
        $model->delete();

        return response('');
    }

    protected function preDelete($model)
    {
    }

    public function success($res = ['message' => 'Success.'])
    {
        return response()->json($res);
    }
}
