<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $this->validate(
            $request,
            [
                'user_id' => 'required|exists:users,id',
                'password' => 'required|max:63|min:8'
            ]
        );

        $user = User::find($request->user_id);

        $code = 422;
        $token = null;

        if ($user && Hash::check($request->password, $user->password)) {
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

    public function me()
    {
        return response()->json(Auth::user());
    }
}
