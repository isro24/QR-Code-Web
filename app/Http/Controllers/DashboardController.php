<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index()
    {
        $todayRevenue = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'paid')
            ->sum('total_price');

        $weekRevenue = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('status', 'paid')
            ->sum('total_price');
        $revenueLast7Days = Order::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('status', 'paid')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->translatedFormat('d M'),
                    'total' => $item->total,
                ];
            });

        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'totalTransactions' => Order::count(),
            'pendingTransactions' => Order::where('status', 'pending')->count(),
            'todayRevenue' => $todayRevenue,
            'weekRevenue' => $weekRevenue,
            'revenueLast7Days' => $revenueLast7Days,
        ]);
    }

}
