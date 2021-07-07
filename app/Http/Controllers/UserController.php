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
        $this->validate($this->request, [
          'name' => 'required',
          'password' => 'required',
        ]);
        $this->validate_params();

        $params = $this->request->all();
        $params['password'] = Hash::make($params['password']);

        $user = User::create($params);

        return response()->json($user);
    }

    public function read($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update($id)
    {
        $this->validate($this->request, ['password' => 'required']);
        $this->validate_params();

        $params = $this->request->all();
        $params['password'] = Hash::make($params['password']);

        $user = User::findOrFail($id)->update($params);

        return response($user);
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return response('');
    }

    public function login($id)
    {
        $this->validate($this->request, ['password' => 'required|max:63|min:8']);

        $password = $this->request->password;

        $user = User::find($id);
        $code = 422;
        $token = null;

        if ($user && Hash::check($password, $user->password)) {
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

    public function validate_params()
    {
        return $this->validate(
            $this->request,
            [
                'name' => 'max:63|min:1',
                'password' => 'max:63|min:8',
                'email' => 'nullable|email'
            ]
        );
    }
}
