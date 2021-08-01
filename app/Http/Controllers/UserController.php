<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends CruldController
{
    protected $model = User::class;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['list']]);
    }
}
