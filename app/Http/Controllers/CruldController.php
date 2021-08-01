<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

        $model = $this->model::create($request->all());

        if ($model->has('user_id')) {
            $model->user_id = Auth::user()->id;
            $model->save();
        }

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
        if ($this->gate_edit && Gate::denies($this->gate_edit, $this->model))
            abort(403);

        $rules = $this->model::$rules;

        $this->validate($request, $rules);
        
        $params = $request->all();
        $model = $this->model::findOrFail($id);
        $model->update($params);

        return response()->json($model);
    }

    public function delete(int $id)
    {
        if ($this->gate_edit && Gate::denies($this->gate_edit, $this->model))
        abort(403);

        $model = $this->model::findOrFail($id);
        
        $model->preDelete($model);
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
