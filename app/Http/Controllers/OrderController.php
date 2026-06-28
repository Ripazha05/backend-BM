<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderDetails.product', 'payment'])
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'orderDetails.product', 'payment'])->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Diproses,Selesai,Dibatalkan',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json($order);
    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();
        return response()->json(['message' => 'Pesanan berhasil dihapus.']);
    }
}
