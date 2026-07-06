<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = [];

        // 1. Notifikasi stok menipis / habis
        $lowStockProducts = Product::where('stock', '<=', 5)->get();

        foreach ($lowStockProducts as $product) {
            $isEmpty = $product->stock <= 0;
            $notifications[] = [
                'id'          => 'stock-' . $product->id_product,
                'type'        => 'low_stock',
                'title'       => $isEmpty ? 'Stok Habis' : 'Stok Menipis',
                'description' => $isEmpty
                    ? "{$product->product_name} kehabisan stok."
                    : "{$product->product_name} tersisa {$product->stock} unit.",
                'time'        => $product->updated_at,
            ];
        }

        // 2. Notifikasi pesanan baru (5 pesanan terakhir dengan status pending/baru)
        if (class_exists(Order::class)) {
            $recentOrders = Order::where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentOrders as $order) {
                $notifications[] = [
                    'id'          => 'order-' . $order->id,
                    'type'        => 'new_order',
                    'title'       => 'Pesanan Baru',
                    'description' => "Pesanan #{$order->id} baru saja masuk.",
                    'time'        => $order->created_at,
                ];
            }
        }

        // Urutkan berdasarkan waktu terbaru
        usort($notifications, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return response()->json([
            'notifications' => $notifications,
        ], 200);
    }
}
