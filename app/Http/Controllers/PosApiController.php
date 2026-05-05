<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class PosApiController extends Controller
{
    public function saveOrder(Request $request)
    {
        $data = $request->validate([
            'orderNumber'    => 'required',
            'tableNumber'    => 'nullable|string',
            'subtotal'       => 'required|numeric',
            'tax'            => 'required|numeric',
            'total'          => 'required|numeric',
            'paymentMethod'  => 'required|string',
            'amountReceived' => 'required|numeric',
            'changeGiven'    => 'required|numeric',
            'items'          => 'required|array|min:1',
            'items.*.productId'   => 'required|exists:products,id',
            'items.*.productName' => 'required|string',
            'items.*.price'       => 'required|numeric',
            'items.*.quantity'    => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'order_number'    => (string) $data['orderNumber'],
            'table_number'    => $data['tableNumber'] ?? null,
            'subtotal'        => $data['subtotal'],
            'tax'             => $data['tax'],
            'total'           => $data['total'],
            'payment_method'  => $data['paymentMethod'],
            'amount_received' => $data['amountReceived'],
            'change_given'    => $data['changeGiven'],
        ]);

        foreach ($data['items'] as $item) {
            $order->items()->create([
                'product_id'   => $item['productId'],
                'product_name' => $item['productName'],
                'price'        => $item['price'],
                'quantity'     => $item['quantity'],
            ]);

            // Increment sold count
            Product::where('id', $item['productId'])->increment('sold', $item['quantity']);
        }

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }
}
