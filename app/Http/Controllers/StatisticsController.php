<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Tshirt_image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request): View
    {
        $selectedYear = $request->query('year', now()->year);

        
        $totalCategories = Category::count();
        $totalColors = Color::count();
        $totalUsers = User::count();
        $totalCustomers = Customer::count();
        $totalTshirtImages = Tshirt_image::count();
        $totalOrders = Order::count();

        
        $usersByType = User::select('user_type', DB::raw('COUNT(*) as count'))
            ->groupBy('user_type')
            ->get()
            ->pluck('count', 'user_type')
            ->toArray();

        
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        
        $closedOrders = Order::where('status', 'closed');
        $totalRevenue = (clone $closedOrders)->sum('total_price');
        $avgOrderValue = (clone $closedOrders)->avg('total_price');
        $maxOrderValue = (clone $closedOrders)->max('total_price');
        $minOrderValue = (clone $closedOrders)->min('total_price');
        $totalClosedOrders = (clone $closedOrders)->count();

        
        $tshirtsByCategory = Category::withCount('tshirts_images')
            ->orderByDesc('tshirts_images_count')
            ->get();

        
        $salesByMonth = Order::select(
        DB::raw("CAST(strftime('%m', date) AS INTEGER) as month"),
        DB::raw('COUNT(*) as total_orders'),
        DB::raw('SUM(total_price) as total_revenue')
    )
        ->where('status', 'closed')
        ->whereYear('date', $selectedYear) 
        ->groupBy(DB::raw("strftime('%m', date)"))
        ->orderBy('month')
        ->get();

        
        $monthlyData = [];
        $maxMonthlyRevenue = 0;
        for ($m = 1; $m <= 12; $m++) {
            $monthData = $salesByMonth->firstWhere('month', $m);
            $revenue = $monthData ? (float) $monthData->total_revenue : 0;
            $orders = $monthData ? (int) $monthData->total_orders : 0;
            $monthlyData[$m] = [
                'revenue' => $revenue,
                'orders' => $orders,
            ];
            if ($revenue > $maxMonthlyRevenue) {
                $maxMonthlyRevenue = $revenue;
            }
        }

        
        $topCustomers = Customer::select('customers.id', DB::raw('SUM(orders.total_price) as total_spent'), DB::raw('COUNT(orders.id) as total_orders'))
            ->join('orders', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.status', 'closed')
            ->groupBy('customers.id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->with('user')
            ->get();

        
        $topImages = Tshirt_image::select('tshirt_images.*', DB::raw('SUM(order_items.qty) as total_sold'))
            ->join('order_items', 'order_items.tshirt_image_id', '=', 'tshirt_images.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'closed')
            ->groupBy('tshirt_images.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        
        $totalItemsSold = Order_item::whereHas('order', function ($q) {
            $q->where('status', 'closed');
        })->sum('qty');

        
        $availableYears = Order::select(DB::raw("DISTINCT CAST(strftime('%Y', date) AS INTEGER) as year"))
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return view('statistics.index', compact(
            'totalCategories',
            'totalColors',
            'totalUsers',
            'totalCustomers',
            'totalTshirtImages',
            'totalOrders',
            'usersByType',
            'ordersByStatus',
            'totalRevenue',
            'avgOrderValue',
            'maxOrderValue',
            'minOrderValue',
            'totalClosedOrders',
            'tshirtsByCategory',
            'monthlyData',
            'maxMonthlyRevenue',
            'topCustomers',
            'topImages',
            'totalItemsSold',
            'selectedYear',
            'availableYears'
        ));
    }
}
