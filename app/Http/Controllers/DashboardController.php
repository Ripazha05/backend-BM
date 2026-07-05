<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary()
    {
        try {
            /* ── 1. Stat Cards ── */
            $totalOrders    = DB::table('orders')->count();
            $totalDelivered = DB::table('orders')->where('status', 'Selesai')->count();
            $totalCanceled  = DB::table('orders')->where('status', 'Dibatalkan')->count();
            $totalRevenue   = DB::table('orders')->where('status', 'Selesai')->sum('total_price');

            /* ── 2. Tren Revenue 6 Bulan Terakhir (hanya order Selesai) ── */
            $bulanIndo = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
            ];

            $revenueTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $sum = DB::table('orders')
                    ->where('status', 'Selesai')
                    ->whereYear('order_date', $date->year)
                    ->whereMonth('order_date', $date->month)
                    ->sum('total_price');

                $revenueTrend[] = [
                    'bulan'   => $bulanIndo[$date->month],
                    'revenue' => round($sum / 1000000, 1), // dikonversi ke juta
                ];
            }

            /* ── 3. Produk Terlaris (dari order_details, order berstatus Selesai) ── */
            $topProductsRaw = DB::table('order_details')
                ->join('orders', 'order_details.id_order', '=', 'orders.id_order')
                ->join('products', 'order_details.id_product', '=', 'products.id_product')
                ->where('orders.status', 'Selesai')
                ->select('products.product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
                ->groupBy('products.id_product', 'products.product_name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();

            $maxSold = $topProductsRaw->max('total_sold') ?: 1;
            $colors  = ['#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444'];

            $topProducts = $topProductsRaw->values()->map(function ($p, $i) use ($maxSold, $colors) {
                return [
                    'rank'  => $i + 1,
                    'name'  => $p->product_name,
                    'sold'  => (int) $p->total_sold,
                    'pct'   => (int) round(($p->total_sold / $maxSold) * 100),
                    'color' => $colors[$i] ?? '#6b7280',
                ];
            });

            /* ── 4. Pesanan Terkini (5 order terbaru, semua status) ── */
            $recentOrdersRaw = DB::table('orders')
                ->join('users', 'orders.id_user', '=', 'users.id_user')
                ->select('orders.id_order', 'users.name as customer', 'orders.total_price', 'orders.status', 'orders.created_at')
                ->orderByDesc('orders.created_at')
                ->limit(5)
                ->get();

            $recentOrders = $recentOrdersRaw->map(function ($o) {
                $firstProduct = DB::table('order_details')
                    ->join('products', 'order_details.id_product', '=', 'products.id_product')
                    ->where('order_details.id_order', $o->id_order)
                    ->value('products.product_name');

                return [
                    'id'       => 'ORD-' . $o->id_order,
                    'customer' => $o->customer,
                    'product'  => $firstProduct ?? '-',
                    'total'    => (float) $o->total_price,
                    'status'   => $o->status,
                ];
            });

            /* ── 5. Aktivitas Terbaru (gabungan: order baru, pembayaran belum diverifikasi, stok menipis) ── */
            $activities = collect();

            foreach (DB::table('orders')->orderByDesc('created_at')->limit(3)->get() as $o) {
                $activities->push([
                    'color' => $o->status === 'Selesai' ? '#10b981' : ($o->status === 'Dibatalkan' ? '#ef4444' : '#3b82f6'),
                    'text'  => "Pesanan #ORD-{$o->id_order} berstatus \"{$o->status}\"",
                    'bold'  => "#ORD-{$o->id_order}",
                    'time'  => $o->created_at,
                ]);
            }

            foreach (DB::table('payments')->where('payment_status', 'Menunggu Verifikasi')->orderByDesc('created_at')->limit(3)->get() as $pay) {
                $activities->push([
                    'color' => '#ef4444',
                    'text'  => "Pembayaran untuk #ORD-{$pay->id_order} belum terkonfirmasi",
                    'bold'  => "#ORD-{$pay->id_order}",
                    'time'  => $pay->created_at,
                ]);
            }

            foreach (DB::table('products')->where('stock', '<=', 5)->get() as $prod) {
                $activities->push([
                    'color' => '#f59e0b',
                    'text'  => "Stok {$prod->product_name} tersisa {$prod->stock} unit — segera restock",
                    'bold'  => $prod->product_name,
                    'time'  => $prod->updated_at,
                ]);
            }

            $activitiesSorted = $activities->sortByDesc('time')->take(5)->values();

            return response()->json([
                'stats' => [
                    'totalOrders'    => $totalOrders,
                    'totalDelivered' => $totalDelivered,
                    'totalCanceled'  => $totalCanceled,
                    'totalRevenue'   => (float) $totalRevenue,
                ],
                'revenueTrend' => $revenueTrend,
                'topProducts'  => $topProducts,
                'recentOrders' => $recentOrders,
                'activities'   => $activitiesSorted,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data dashboard',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
