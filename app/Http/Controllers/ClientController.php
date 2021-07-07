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
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:31'
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
        $client = Client::findOrFail($id);
        $client->update($params);

        return response()->json($client);
    }

    public function delete($id)
    {
        Client::findOrFail($id)->delete();

        return response('');
    }
}
