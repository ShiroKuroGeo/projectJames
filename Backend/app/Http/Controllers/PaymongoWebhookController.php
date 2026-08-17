<?php

namespace App\Http\Controllers;

use App\Http\Services\BookingServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymongoWebhookController extends Controller
{
    private $bookingServices;

    public function __construct(BookingServices $bookingServices)
    {
        $this->bookingServices = $bookingServices;
    }

    public function handle(Request $request)
    {
        if (!$this->isValidSignature($request)) {
            Log::warning('PayMongo webhook: invalid signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = $request->input('data.attributes.data');
        $eventType = $request->input('data.attributes.type');

        // Only act on the event that means "checkout session paid"
        if ($eventType !== 'checkout_session.payment.paid') {
            return response()->json(['message' => 'Event ignored'], 200);
        }

        $checkoutSession = $payload['attributes'] ?? [];
        $payments = $checkoutSession['payments'] ?? [];
        $latestPayment = $payments[0] ?? null;

        if (!$latestPayment) {
            Log::warning('PayMongo webhook: paid event with no payment data', ['payload' => $payload]);
            return response()->json(['message' => 'No payment data'], 200);
        }

        $bookingCode = $this->extractBookingCode($checkoutSession);

        $updatePayload = [
            'booking_code' => $bookingCode,
            'payment_method' => $latestPayment['attributes']['source']['type'] ?? null,
            'payment_status' => $latestPayment['attributes']['status'] ?? null,
        ];

        $this->bookingServices->attempUpdateAfterPayment($updatePayload);

        return response()->json(['message' => 'Webhook processed'], 200);
    }
    private function isValidSignature(Request $request): bool
    {
        $signatureHeader = $request->header('Paymongo-Signature');
        $webhookSecret = env('PAYMONGO_WEBHOOK_SECRET');

        if (!$signatureHeader || !$webhookSecret) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $pair) {
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, null);
            $parts[$key] = $value;
        }

        $timestamp = $parts['t'] ?? null;
        $expectedSig = $parts['li'] ?? $parts['te'] ?? null;

        if (!$timestamp || !$expectedSig) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $request->getContent();
        $computedSig = hash_hmac('sha256', $signedPayload, $webhookSecret);

        return hash_equals($expectedSig, $computedSig);
    }

    private function extractBookingCode(array $checkoutSession): ?string
    {
        $successUrl = $checkoutSession['success_url'] ?? '';
        parse_str(parse_url($successUrl, PHP_URL_QUERY) ?? '', $query);

        return $query['booking_code'] ?? null;
    }
}
