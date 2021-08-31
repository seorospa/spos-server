<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Product;

class SummaryController extends Controller
{
    public function generateSummary($start, $end = null, $includeTransactions = true)
    {
        $arr = [];
        $arr['categories'] = [];
        
        if ($end) {
          $queryMethod = 'whereBetween';
          $dates = [$start, $end];
        } else {
          $queryMethod = 'whereDate';
          $dates = $start;
        }

        $paid = Ticket::{$queryMethod}('updated_at', $dates)->where('status', 'paid')->get(['cart', 'status']);
        $totalRevenue = 0;

        foreach ($paid as $ticket) {
            $products = array_keys($ticket->cart);

            if (!empty($products)) {
              foreach ($products as $code) {
                  $product = Product::where('code', $code)->first();
                  $qty = $ticket->cart[$code]['qty'];
                  $category = Category::find($product->category);
                  $row = isset($arr[$product->category]) ? $arr[$product->category] : ['price' => 0, 'name' => $category->name];

                  $row['price'] += $product->price * $qty;
                  $totalRevenue += $row['price'];
                  $arr['categories'][$product->category] = $row;
              }
            }
        }

        if ($includeTransactions) {
          $trans = Transaction::{$queryMethod}('updated_at', $dates)->get(['amount', 'reason']);
          $arr['transactions'] = $trans;
        }

        $arr['start'] = $start;
        $arr['end'] = $end;
        $arr['totalRevenue'] = $totalRevenue;

        return $arr;
    }

    public function simple(Request $request)
    {
        $start = $request->input('start', date('Y-m-d'));
        $end = $request->input('end') ? $request->input('end', date('Y-m-d')) : date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        return response()->json($this->generateSummary($start, $end));
    }

    public function latestSales(Request $request)
    {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));

        # TODO: reescribir esto
        $daily = $this->generateSummary($today, false);
        $weekly = $this->generateSummary(date('Y-m-d', strtotime($today . ' -1 week')), $tomorrow, false);
        $monthly = $this->generateSummary(date('Y-m-d', strtotime($today . ' -1 month')), $tomorrow, false);
        $historical = $this->generateSummary('1900-01-01', $tomorrow, false);

        return response()->json([
          'hoy' => $daily,
          'semana' => $weekly,
          'mes' => $monthly,
          'histórico' => $historical,
        ]);
    }
    
    public function stockAlerts(Request $request)
    {
      $products = Product::whereColumn('products.min', '<', 'products.qty')->get(['title', 'qty', 'min']);

      return response()->json($products);
    }
}
