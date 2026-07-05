<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use Illuminate\Http\JsonResponse;

class OwnerApiController extends Controller
{
    // 1. API untuk Dashboard
    public function getDashboardData(): JsonResponse
    {
        $totalPendapatanKotor = Order::sum('total_price');
        $totalProduk = Product::count();
        $pesananMasuk = Order::where('status', 'pending')->count();
        $pembayaranTerverifikasi = Payment::where('payment_status', 'verified')->count();

        // Ambil order terbaru beserta relasi user (jika ada)
        $orders = Order::orderBy('created_at', 'desc')->take(5)->get();

        // Ambil log pembayaran terbaru
        $payments = Payment::orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'stats' => [
                'total_pendapatan' => $totalPendapatanKotor,
                'total_produk' => $totalProduk,
                'pesanan_masuk' => $pesananMasuk,
                'pembayaran_terverifikasi' => $pembayaranTerverifikasi,
            ],
            'orders' => $orders,
            'payments' => $payments
        ]);
    }

    // 2. API untuk Stock Gudang
    public function getStockData(): JsonResponse
    {
        // Pastikan model Product sudah memiliki relasi 'category' ke model Category
        $products = Product::with('category')->get();
        return response()->json($products);
    }

    // 3. API untuk Laporan Keuangan
    public function getFinancialData(): JsonResponse
    {
        $totalPendapatanKotor = Order::sum('total_price');

        // Ambil semua detail order untuk breakdown tabel
        $orderDetails = OrderDetail::with('product')->get();

        return response()->json([
            'total_pendapatan' => $totalPendapatanKotor,
            'order_details' => $orderDetails
        ]);
    }
}
