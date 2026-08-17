<?php

namespace App\Http\Controllers;

use App\Http\Services\BookingServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $bookingServices;

    public function __construct(BookingServices $bookingServices)
    {
        $this->bookingServices = $bookingServices;
    }

    public function createCheckout(Request $request)
    {
        $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'show_line_items' => true,
                        'payment_method_types' => ['gcash', 'paymaya'],
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount' => $request->amount,
                                'name' => $request->booking_code,
                                'quantity' => 1,
                            ],
                            [
                                'currency' => 'PHP',
                                'amount' => 10 * 100,
                                'name' => 'Reservation Fee',
                                'quantity' => 1,
                            ],
                        ],
                        'success_url' => config('services.frontend_url') . 'payment?status=success&booking_code=' . $request->booking_code,
                        'cancel_url' => config('services.frontend_url') . 'payment?status=failed&booking_code=' . $request->booking_code,
                    ],
                ],
            ]);

        $result = $response->json();

        if ($response->successful()) {
            return response()->json([
                'message' => 'Checkout session created successfully.',
                'data' => [
                    'checkout_url' => $result['data']['attributes']['checkout_url'],
                    'checkout_session_id' => $result['data']['id'],
                    'status' => $result['data']['attributes']['status'],
                ],
            ], 200);
        }

        return response()->json([
            'message' => 'Something is wrong. Please try again.',
            'data' => [],
            'errors' => $result['errors'] ?? [],
        ], $response->status());
    }
}
