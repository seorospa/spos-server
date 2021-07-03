<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientController extends Controller
{
    public function list()
    {
        $clients = Client::all(['id', 'name']);
        return response()->json($clients);
    }

    public function create()
    {
        $this->validate(
            $this->request,
            [
                'name' => 'required|max:31',
                'email' => 'nulleable|email',
                'phone' => 'string|max:31'
            ]
        );

        $client = Client::create($this->request->all());

        return response()->json($client);
    }

    public function read($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    public function update($id)
    {
        $params = $this->request->all();
        Client::findOrFail($id)->update($params);

        return response('');
    }

    public function delete($id)
    {
        Client::findOrFail($id)->delete();

        return response('');
    }
}
