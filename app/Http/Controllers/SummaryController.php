<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\Transaction;

class SummaryController extends Controller
{
    public function simple()
    {
        $date = $this->request->input('data', date('Y-m-d'));

        $tickets = Ticket::whereDate('updated_at', $date);
        $trans = Transaction::whereDate('updated_at', $date)->get(['amount', 'reason']);
        
        $paid = $tickets->where('status', 'paid')->get();

        $arr = [];
        foreach ($paid as $ticket) {
            $products = $ticket->products;

            foreach ($products as $product) {
                $category = Category::find($product->category);
                $row = isset($arr[$product->category]) ? $arr[$product->category] : ['price' => 0, 'name' => $category->name];

                $row['price'] += $product->price * $product->qty;
                $arr[$product->category] = $row;
            }
        }

        $arr['transactions'] = $trans;

        return response()->json($arr);
    }
}
