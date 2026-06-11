<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filterByDate = $request->date;
        $filterByStatus = $request->status;
        $filterByNif = $request->nif;

        $orders = Order::query();

        if (auth()->user()->user_type == 'C') {
            $orders->where('customer_id', auth()->user()->id);
        } elseif (auth()->user()->user_type == 'F') {
            $orders->where('status', 'pending');
        }

        if ($filterByDate) {
            $orders->whereDate('date', $filterByDate);
        }

        if ($filterByNif) {
            $orders->where('nif', $filterByNif);
        }

        if ($filterByStatus) {
            $orders->where('status', $filterByStatus);
        }

        $orders = $orders->with('customer')->orderBy('date', 'desc')->paginate(10)->withQueryString();

        return view('orders.index', compact('orders', 'filterByDate', 'filterByStatus', 'filterByNif'));
    }

    public function show(Order $order): View
    {
        return view('orders.show', compact('order'));
    }
}
