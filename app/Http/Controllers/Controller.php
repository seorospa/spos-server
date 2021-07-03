<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $request;
    
    public function __construct(Request $req)
    {
        $this->request = $req;
        $this->logged = Auth::user();
    }
}
