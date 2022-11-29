<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class PaymentController extends ApiController
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'order_items' => 'required',
            'order_items.*.product_id' => 'required|integer|exists:products,id',
            'order_items.*.quantity' => 'required|integer',
            'request_from' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        $totalAmount = 0;
        $deliveryAmount = 0;
        foreach ($request->order_items as $orderItem) {

            $product = Product::FindOrFail($orderItem['product_id']);
            if ($product->quantity < $orderItem['quantity']) {

                return $this->errorResponse('the product quantity is incorect', 400);
            }

            $totalAmount += $product->price * $orderItem['quantity'];
            $deliveryAmount += $product->delivery_amount;
        }

        $payingAmount = $totalAmount + $deliveryAmount;

        $amounts=[
            'totalAmount'=>$totalAmount,
            'deliveryAmount'=>$deliveryAmount,
            'payingAmount'=>$payingAmount,
        ];
        

        $api = 'test';
        $amount = $payingAmount.'0';
        $mobile = "شماره موبایل";
        $factorNumber = "شماره فاکتور";
        $description = "توضیحات";
        $redirect = 'http://localhost:8000/payment/verify';  //in web routes
        $result = $this->sendRequest($api, $amount, $redirect, $mobile, $factorNumber, $description);
        $result = json_decode($result);
        if ($result->status) {
            OrderController::create($request,$amounts,$result->token);
            $go = "https://pay.ir/pg/$result->token";
            return $this->successResponse($go, 200);
        } else {

            return $this->errorResponse($result->errorMessage, 422);
        }
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $api = 'test';
        $token = $request->token;
        $result = json_decode($this->verifyRequest($api, $token));
        return $result;
        if (isset($result->status)) {
            if(Transaction::where('trans_id',$result->transId)->exists())
            {
                return $this->errorResponse('تراکنش تکراری است' , 422);
            }
            OrderController::update($token,$result->transId);
            if ($result->status == 1) {
                return $this->successResponse('تراکنش با موفقیت انجام شد',200);
            } else {
                return $this->errorResponse('تراکنش با خطا مواجه شد',422);
            }
        } else {
            if ($request->status == 0) {
                return $this->errorResponse('تراکنش با خطا مواجه شد',422);
            }
        }
    }

    public function sendRequest($api, $amount, $redirect, $mobile = null, $factorNumber = null, $description = null)
    {
        return $this->curl_post('https://pay.ir/pg/send', [
            'api'          => $api,
            'amount'       => $amount,
            'redirect'     => $redirect,
            'mobile'       => $mobile,
            'factorNumber' => $factorNumber,
            'description'  => $description,
        ]);
    }

    public function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    public function verifyRequest($api, $token)
    {
        return $this->curl_post('https://pay.ir/pg/verify', [
            'api'     => $api,
            'token' => $token,
        ]);
    }
}
