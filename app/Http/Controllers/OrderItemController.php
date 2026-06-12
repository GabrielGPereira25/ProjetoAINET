<?php

namespace App\Http\Controllers;

use App\Models\Order_item;
use App\Http\Requests\OrderItemFormRequest;
use App\Http\Controllers\CartController;
use App\Models\Tshirt_image;
use App\Models\Order;
use App\Models\Color;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function index(Request $request)
    {
        $filterByOrderId = $request->query('order_id');
        $filterByTshirtImageId = $request->query('tshirt_image_id');

        $orderItemsQuery = Order_item::query()->with(['order', 'tshirt_image', 'color']);

        if ($filterByOrderId) {
            $orderItemsQuery->where('order_id', $filterByOrderId);
        }
        if ($filterByTshirtImageId) {
            $orderItemsQuery->where('tshirt_image_id', $filterByTshirtImageId);
        }

        $orderItems = $orderItemsQuery->paginate(20)->withQueryString();

        return view('order_items.index', compact('orderItems', 'filterByOrderId', 'filterByTshirtImageId'));
    }

    public function create()
    {
        $orderItem = new Order_item();
        $orders = Order::all();
        $tshirtImages = Tshirt_image::all();
        $colors = Color::all();

        return view('order_items.create', compact('orderItem', 'orders', 'tshirtImages', 'colors'));
    }

    public function store(OrderItemFormRequest $request)
    {
        $validated = $request->validated();
        
        if (!isset($validated['unit_price']) || !isset($validated['sub_total'])) {
            $tshirtImage = Tshirt_image::find($validated['tshirt_image_id']);
            $validated['unit_price'] = CartController::calculateUnitPrice($tshirtImage, $validated['qty']);
            $validated['sub_total'] = $validated['unit_price'] * $validated['qty'];
        }

        $newOrderItem = Order_item::create($validated);

        $url = route('order_items.show', ['order_item' => $newOrderItem]);
        $htmlMessage = "Order Item <a href='$url'><strong>{$newOrderItem->id}</strong></a> has been created successfully!";
        
        return redirect()->route('order_items.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function show(Order_item $order_item)
    {
        $order_item->load(['order', 'tshirt_image', 'color']);
        return view('order_items.show', compact('order_item'));
    }

    public function edit(Order_item $order_item)
    {
        $orders = Order::all();
        $tshirtImages = Tshirt_image::all();
        $colors = Color::all();

        return view('order_items.edit', compact('order_item', 'orders', 'tshirtImages', 'colors'));
    }

    public function update(OrderItemFormRequest $request, Order_item $order_item)
    {
        $validated = $request->validated();
        
        if (!isset($validated['unit_price']) || !isset($validated['sub_total'])) {
            $tshirtImage = Tshirt_image::find($validated['tshirt_image_id']);
            $validated['unit_price'] = CartController::calculateUnitPrice($tshirtImage, $validated['qty']);
            $validated['sub_total'] = $validated['unit_price'] * $validated['qty'];
        }

        $order_item->update($validated);

        $url = route('order_items.show', ['order_item' => $order_item]);
        $htmlMessage = "Order Item <a href='$url'><strong>{$order_item->id}</strong></a> has been updated successfully!";
        
        return redirect()->route('order_items.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Order_item $order_item)
    {
        $id = $order_item->id;
        $order_item->delete();

        $alertType = 'success';
        $alertMsg = "Order Item ($id) has been deleted successfully!";
        
        return redirect()->route('order_items.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
}
