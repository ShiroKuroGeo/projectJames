<?php

use App\Models\Payments;
use Illuminate\Http\Request;

class PaymentServices
{

    public function createPayment(Request $request)
    {
        try {

            $validation = $request->validate([
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'user_id' => ['request', 'integer', 'exits:users,id'],
                'payment_type' => ['nullable']
            ]);

            $payment = Payments


        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'message' => $th->getMessage(),
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }
}
