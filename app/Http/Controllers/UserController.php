<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function list()
    {
        $users = User::all(['id', 'name']);
        return response()->json($users);
    }

    public function create()
    {
        $this->validate(
            $this->request,
            [
                'name' => 'required|max:31',
                'password' => 'required|max:31',
                'email' => 'email'
            ]
        );

        $params = $this->request->all();
        $params['password'] = Hash::make($params['password']);

        User::create($params);

        return response('');
    }

    public function read($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update($id)
    {
        $this->validate(
            $this->request,
            [
                'name' => 'required|max:31',
                'password' => 'required|max:31'
            ]
        );

        $params = $this->request->all();
        $params['password'] = Hash::make($params['password']);

        User::findOrFail($id)->update($params);

        return response('');
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return response('');
    }

    public function login()
    {
        $this->validate(
            $this->request,
            [
                'id' => 'required|integer',
                'password' => 'required|max:63'
            ]
        );

        $params = (object)$this->request->all();

        $user = User::find($params->id);
        $code = 422;
        $token = [];

        if ($user && Hash::check($params->password, $user->password)) {
            $code = 200;
            $token = $user->generateJWT();
        }

        return response()->json($token, $code);
    }

    public function refresh()
    {
        $logged = (object)Auth::user();
        return response()->json($logged->generateJWT());
    }
}
