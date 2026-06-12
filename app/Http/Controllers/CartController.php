<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartConfirmationFormRequest;
use App\Models\Color;
use App\Models\Order;
use App\Models\Price;
use App\Models\Tshirt_image;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Mail\OrderPending;

class CartController extends Controller
{
    public static function calculateUnitPrice(Tshirt_image $tshirt_image, $qty): float
    {
        $price = Price::first();
        if ($price->qty_discount <= $qty) {
            if ($tshirt_image->customer) {
                return $price->unit_price_own_discount;
            }
            return $price->unit_price_catalog_discount;
        }
        if ($tshirt_image->customer) {
            return $price->unit_price_own;
        }
        return $price->unit_price_catalog;
    }
    public function show(): View
    {
        $cart = session('cart', []);
        $colors = Color::all();
        $total_items = array_sum(array_column($cart, 'qty'));
        $total_price = array_sum(array_column($cart, 'sub_total'));

        return view('cart.show', compact('cart', 'colors', 'total_items', 'total_price'));
    }

    public function addToCart(Request $request, Tshirt_image $tshirt_image): RedirectResponse
    {
        $cart = session('cart', []);
        $id = $tshirt_image->id . '_' . $request->size . '_' . $request->color;


        if (array_key_exists($id, $cart)) {
            $cart[$id]['qty'] += $request->qty;
        } else {
            $cart[$id] = [
                'tshirt_image_id' => $tshirt_image->id,
                'tshirt_image_url' => $tshirt_image->imageFullUrl,
                'tshirt_image_name' => $tshirt_image->name,
                'unit_price' => 0,
                'sub_total' => 0,
                'size' => $request->size,
                'color' => $request->color,
                'qty' => $request->qty
            ];
        }
        $cart[$id]['unit_price'] = self::calculateUnitPrice($tshirt_image, $cart[$id]['qty']);
        $cart[$id]['sub_total'] = $cart[$id]['unit_price'] * $cart[$id]['qty'];
        session(['cart' => $cart]);

        return back()
            ->with('alert-msg', 'Item added to cart successfully!')
            ->with('alert-type', 'success');
    }

    public function removeFromCart(Request $request, $id): RedirectResponse
    {
        $cart = session('cart', []);
        if (array_key_exists($id, $cart)) {
            unset($cart[$id]);
            session(['cart' => $cart]);
            return back()
                ->with('alert-msg', 'Item removed from cart successfully!')
                ->with('alert-type', 'success');
        } else {
            return back()
                ->with('alert-msg', 'Item not found in cart!')
                ->with('alert-type', 'danger');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');
        return back()
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Shopping Cart has been cleared');
    }

    public function confirm(CartConfirmationFormRequest $request): RedirectResponse
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'Your cart is empty!');
        }
        DB::beginTransaction();
        try {
            $order = Order::create([
                'date' => now(),
                'nif' => $request->nif,
                'address' => $request->address,
                'payment_type' => $request->payment_type,
                'payment_ref' => $request->payment_ref,
                'total_price' => array_sum(array_column($cart, 'sub_total')),
                'notes' => $request->notes,
                'customer_id' => auth()->id(),
                'status' => 'pending',
            ]);

            foreach ($cart as $item) {
                $order->order_items()->create([
                    'tshirt_image_id' => $item['tshirt_image_id'],
                    'color_code' => $item['color'],
                    'size' => $item['size'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'sub_total' => $item['sub_total']
                ]);
            }

            DB::commit();

            Mail::to(auth()->user()->email)->send(new OrderPending($order));

            session()->forget('cart');
            return redirect()->route('orders.show', $order)
                ->with('alert-type', 'success')
                ->with('alert-msg', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'An error occurred while placing your order. Please try again.');
        }
    }
    public function updateQty(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);
        $cart = session('cart', []);
        if (array_key_exists($id, $cart)) {
            $cart[$id]['qty'] = $request->qty;
            $cart[$id]['unit_price'] = self::calculateUnitPrice(Tshirt_image::find($cart[$id]['tshirt_image_id']), $cart[$id]['qty']);
            $cart[$id]['sub_total'] = $cart[$id]['unit_price'] * $cart[$id]['qty'];
            session(['cart' => $cart]);
            return back()
                ->with('alert-type', 'success')
                ->with('alert-msg', "Quantity updated successfully!");
        }else {
            return back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "Item not found in cart!");
        }
    }

    public function updateSize(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'size' => 'required|in:XS,S,M,L,XL',
        ]);
        $cart = session('cart', []);
        $newId = $cart[$id]['tshirt_image_id'] . '_' . $request->size . '_' . $cart[$id]['color'];
        if ($id === $newId) {
            return back()
                ->with('alert-type', 'success')
                ->with('alert-msg', "The size is the same!");
        }
        if (array_key_exists($id, $cart)) {
            if (array_key_exists($newId, $cart)) {
                $cart[$newId]['qty'] += $cart[$id]['qty'];
                unset($cart[$id]);
            } else {
                $cart[$newId] = [
                    'tshirt_image_id' => $cart[$id]['tshirt_image_id'],
                    'tshirt_image_url' => $cart[$id]['tshirt_image_url'],
                    'tshirt_image_name' => $cart[$id]['tshirt_image_name'],
                    'size' => $request->size,
                    'color' => $cart[$id]['color'],
                    'qty' => $cart[$id]['qty'],
                    'unit_price' => $cart[$id]['unit_price'],
                    'sub_total' => $cart[$id]['sub_total'],
                ];
                unset($cart[$id]);
            }
            $cart[$newId]['unit_price'] = self::calculateUnitPrice(Tshirt_image::find($cart[$newId]['tshirt_image_id']), $cart[$newId]['qty']);
            $cart[$newId]['sub_total'] = $cart[$newId]['unit_price'] * $cart[$newId]['qty'];

            session(['cart' => $cart]);
            return back()
                ->with('alert-type', 'success')
                ->with('alert-msg', "Size updated successfully!");
        }else {
            return back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "Item not found in cart!");
        }
    }

    public function updateColor(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'color' => 'required|exists:colors,code',
        ]);
        $cart = session('cart', []);
        $newId = $cart[$id]['tshirt_image_id'] . '_' . $cart[$id]['size'] . '_' . $request->color;
        if ($id === $newId) {
            return back()
                ->with('alert-type', 'success')
                ->with('alert-msg', "The color is the same!");
        }
        if (array_key_exists($id, $cart)) {
            if (array_key_exists($newId, $cart)) {
                $cart[$newId]['qty'] += $cart[$id]['qty'];
                unset($cart[$id]);
            } else {
                $cart[$newId] = [
                    'tshirt_image_id' => $cart[$id]['tshirt_image_id'],
                    'tshirt_image_url' => $cart[$id]['tshirt_image_url'],
                    'tshirt_image_name' => $cart[$id]['tshirt_image_name'],
                    'size' => $cart[$id]['size'],
                    'color' => $request->color,
                    'qty' => $cart[$id]['qty'],
                    'unit_price' => $cart[$id]['unit_price'],
                    'sub_total' => $cart[$id]['sub_total'],
                ];
                unset($cart[$id]);
            }
            $cart[$newId]['unit_price'] = self::calculateUnitPrice(Tshirt_image::find($cart[$newId]['tshirt_image_id']), $cart[$newId]['qty']);
            $cart[$newId]['sub_total'] = $cart[$newId]['unit_price'] * $cart[$newId]['qty'];

            session(['cart' => $cart]);
            return back()
                ->with('alert-type', 'success')
                ->with('alert-msg', "Size updated successfully!");
        }else {
            return back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "Item not found in cart!");
        }
    }
}
