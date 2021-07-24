<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TransactionController extends Controller
{
    public function list()
    {
        $transactions = Transaction::all(['id', 'amount', 'reason', 'debit_or_credit', 'user']);
        return response()->json($transactions);
    }

    public function create()
    {
        $this->validate_params();

        $params = $this->request->all();
        $params['user'] = $this->logged->id;

        $transaction = Transaction::create($params);

        return response()->json($transaction);
    }

    public function read($id)
    {
        $transaction = Transaction::findOrFail($id);
        return response()->json($transaction);
    }

    public function update($id)
    {
        $this->validate_params();

        $params = $this->request->all();
        $params['user'] = $this->logged->id;

        $user = Transaction::findOrFail($id)->update($params);

        return response($user);
    }

    public function delete($id)
    {
        Transaction::findOrFail($id)->delete();

        return response('');
    }

    public function validate_params()
    {
        return $this->validate(
            $this->request,
            [
              'amount' => 'required',
              'reason' => 'required',
              'debit_or_credit' => 'required',
            ]
        );
    }
}
