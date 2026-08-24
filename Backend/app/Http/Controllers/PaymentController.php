<?php

namespace App\Http\Controllers;

use App\Http\Services\BookingServices;
use App\Http\Services\PaymentServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $bookingServices;
    private $paymentServices;

    public function __construct(BookingServices $bookingServices, PaymentServices $paymentServices)
    {
        $this->bookingServices = $bookingServices;
        $this->paymentServices = $paymentServices;
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
                        'payment_method_types' => ['qrph'],
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

    public function getPaymentMethod(Request $request){
        return $this->paymentServices->attemptGetPaymentMethod($request);
    }

    public function submitPayment(Request $request){
        return $this->paymentServices->attemptSubmitPayment($request);
    }

    public function createPayment(Request $request){
        return $this->paymentServices->attemptCreatePayment($request);
    }
}
