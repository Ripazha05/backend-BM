<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Batas minimum stok dianggap "menipis"
    const LOW_STOCK_THRESHOLD = 5;

    public function index()
    {
        $notifications = collect();

        // 1. STOK MENIPIS
        $lowStockProducts = Product::where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock', 'asc')
            ->get();

        foreach ($lowStockProducts as $product) {
            $notifications->push([
                'id'          => 'stock-' . $product->id_product,
                'type'        => 'low_stock',
                'title'       => 'Stok hampir habis',
                'description' => "{$product->product_name} tersisa {$product->stock} unit",
                'time'        => $product->updated_at,
                'is_read'     => false,
            ]);
        }

        // 2. PESANAN BARU MASUK (status Pending)
        $newOrders = Order::with('user')
            ->where('status', 'Pending')
            ->orderBy('order_date', 'desc')
            ->limit(10)
            ->get();

        foreach ($newOrders as $order) {
            $customerName = $order->user->name ?? 'Customer';
            $notifications->push([
                'id'          => 'order-new-' . $order->id_order,
                'type'        => 'new_order',
                'title'       => 'Pesanan baru masuk',
                'description' => "Order #{$order->id_order} dari {$customerName}",
                'time'        => $order->order_date,
                'is_read'     => false,
            ]);
        }

        // 3. PESANAN SELESAI / DIKIRIM TERBARU
        $completedOrders = Order::with('user')
            ->where('status', 'Selesai')
            ->orderBy('order_date', 'desc')
            ->limit(5)
            ->get();

        foreach ($completedOrders as $order) {
            $notifications->push([
                'id'          => 'order-done-' . $order->id_order,
                'type'        => 'order_done',
                'title'       => 'Pengiriman dikonfirmasi',
                'description' => "Order #{$order->id_order} berhasil dikirim",
                'time'        => $order->order_date,
                'is_read'     => false,
            ]);
        }

        // Urutkan semua notifikasi dari yang terbaru
        $sorted = $notifications->sortByDesc('time')->values();

        return response()->json([
            'total_unread'  => $sorted->count(),
            'notifications' => $sorted,
        ]);
    }
}
