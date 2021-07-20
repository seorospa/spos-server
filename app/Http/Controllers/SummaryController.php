<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;

class SummaryController extends Controller
{
    public function simple()
    {
        $tickets = Ticket::whereDate('updated_at', '=', date('Y-m-d'))->where('status', 'paid')->get();

        $arr = [];
        foreach ($tickets as $ticket) {
            $products = json_decode($ticket->products);

            foreach ($products as $product) {
                $category = Category::find($product->category);
                $row = isset($arr[$product->category]) ? $arr[$product->category] : ['price' => 0, 'name' => $category->name];

                $row['price'] += $product->price * $product->qty;
                $arr[$product->category] = $row;
            }
        }

        return response()->json($arr);
    }
}
