<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function cancel(Request $request, Order $order)
    {
        if($order->status === 'pending'){

            $order->status = 'cancelled';
            $order->reason_for_cancelation = $request->reason;
            $order->save();

            return back()->with('alert-msg', 'Order Canceled Successfully')->with('alert-type', 'success');
        } else {
            return back()->with('alert-type', 'error')->with('alert-msg', 'Only pending orders can be cancelled.');
        }
    }

    public function updateStatus(Request $request, Order $order){
        if($order->status === 'pending'){
            if($request->status === 'closed'){
                $order->status = 'closed';
                $pdf = Pdf::loadView('orders.order-to-pdf', ['order' => $order]);
                $pdf->save(storage_path('app/private/pdf_receipts/receipt_' . $order->id . '.pdf'));
                $order->save();
                return back()->with('alert-msg', 'Order Completed Successfully')->with('alert-type', 'success');
            } else {
                return view('orders.cancel', compact('order'));
            }
        } else {
            return back()->with('alert-type', 'error')->with('alert-msg', 'Only pending orders can be completed.');
        }
    }
}
