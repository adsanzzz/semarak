<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->status = $request->status;
        $order->save();

        return back();
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return back();
    }
}