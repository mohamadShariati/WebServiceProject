<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class OrderController extends ApiController
{
    public static function create($request, $amounts,$token)
    {
        DB::beginTransaction();
        $order = Order::create([
            'user_id'=>$request->user_id,
            'total_amount'=>$amounts['totalAmount'],
            'delivery_amount'=>$amounts['deliveryAmount'],
            'paying_amount'=>$amounts['payingAmount']
        ]);

        foreach ($request->order_items as $oreder_item ) {
            $product=Product::FindOrFail($oreder_item['product_id']);
            OrderItem::create([
                'order_id'=>$order->id,
                'product_id'=>$product->id,
                'price'=>$product->price,
                'quantity'=>$oreder_item['quantity'],
                'subtotal'=>$product->price*$oreder_item['quantity']
            ]);
        }

        Transaction::create([
            'user_id'=>$request->user_id,
            'order_id'=>$order->id,
            'amount'=>$amounts['payingAmount'],
            'token'=>$token,
            
        ]);
        Db::commit();
    }

    public static function update($token,$transId)
    {
        DB::beginTransaction();
        //find transaction

        $transaction=Transaction::where('token',$token)->firstOrFail();
        $transaction->update([
            'status'=>1,
            'trance_id'=>$transId
        ]);

        $order=Order::findOrFail($transaction->order_id);
        $order->update([
            'status'=>1,
            'payment_status'=>1
        ]);

        foreach (OrderItem::where('order_id',$order->id)->get() as $item) {
            $product=Product::find($item->product_id);
            $product->update([
                'quantity'=> ($product->quantity - $item->quantity)
            ]);
        }
        DB::commit();
    }
}
