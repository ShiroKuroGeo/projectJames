<?php

namespace App\Http\Controllers;

use App\Http\Services\BookingServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymongoWebhookController extends Controller
{
    private BookingServices $bookingServices;

    public function __construct(BookingServices $bookingServices)
    {
        $this->bookingServices = $bookingServices;
    }

    public function handle(Request $request)
    {
        $rawPayload = $request->getContent();

        Log::info('PayMongo webhook received', [
            'ip' => $request->ip(),
            'content_length' => strlen($rawPayload),
        ]);

        if (!$this->isValidSignature($request, $rawPayload)) {
            Log::warning('PayMongo webhook rejected: invalid signature');

            return response()->json([
                'message' => 'Invalid signature',
            ], 400);
        }

        $event = json_decode($rawPayload, true);

        if (!is_array($event)) {
            Log::warning('PayMongo webhook rejected: invalid JSON');

            return response()->json([
                'message' => 'Invalid payload',
            ], 400);
        }

        $eventType = data_get(
            $event,
            'data.attributes.type'
        );

        $eventId = data_get(
            $event,
            'data.id'
        );

        Log::info('PayMongo webhook event', [
            'event_id' => $eventId,
            'event_type' => $eventType,
        ]);

        if ($eventType !== 'checkout_session.payment.paid') {

            Log::info('PayMongo webhook ignored', [
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);

            return response()->json([
                'message' => 'Event ignored',
            ], 200);
        }

        $checkoutSession = data_get(
            $event,
            'data.attributes.data.attributes',
            []
        );

        if (!is_array($checkoutSession)) {

            Log::warning(
                'PayMongo webhook: checkout session data missing',
                [
                    'event_id' => $eventId,
                ]
            );

            return response()->json([
                'message' => 'Checkout session data missing',
            ], 200);
        }

        $payments = $checkoutSession['payments'] ?? [];

        if (!is_array($payments) || empty($payments)) {

            Log::warning(
                'PayMongo webhook: no payment found',
                [
                    'event_id' => $eventId,
                ]
            );

            return response()->json([
                'message' => 'No payment found',
            ], 200);
        }

        $payment = collect($payments)->first(function ($payment) {

            return data_get(
                $payment,
                'attributes.status'
            ) === 'paid';
        });

        if (!$payment) {

            Log::warning(
                'PayMongo webhook: no paid payment found',
                [
                    'event_id' => $eventId,
                ]
            );

            return response()->json([
                'message' => 'No paid payment found',
            ], 200);
        }

        $paymentId = data_get(
            $payment,
            'id'
        );

        $paymentStatus = data_get(
            $payment,
            'attributes.status'
        );

        $paymentMethod = data_get(
            $payment,
            'attributes.source.type'
        );

        $bookingCode = $this->extractBookingCode(
            $checkoutSession
        );

        if (!$bookingCode) {

            Log::error(
                'PayMongo webhook: booking code missing',
                [
                    'event_id' => $eventId,
                    'payment_id' => $paymentId,
                ]
            );

            return response()->json([
                'message' => 'Booking code missing',
            ], 200);
        }

        Log::info('PayMongo payment confirmed', [
            'event_id' => $eventId,
            'payment_id' => $paymentId,
            'booking_code' => $bookingCode,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod,
        ]);

        try {

            $updatePayload = [
                'booking_code' => $bookingCode,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'payment_id' => $paymentId,
            ];

            $this->bookingServices
                ->attempUpdateAfterPayment($updatePayload);
        } catch (\Throwable $exception) {

            Log::error(
                'PayMongo webhook: booking update failed',
                [
                    'event_id' => $eventId,
                    'payment_id' => $paymentId,
                    'booking_code' => $bookingCode,
                    'error' => $exception->getMessage(),
                ]
            );

            return response()->json([
                'message' => 'Webhook processing failed',
            ], 500);
        }

        return response()->json([
            'message' => 'Payment processed successfully',
        ], 200);
    }

    private function isValidSignature(
        Request $request,
        string $rawPayload
    ): bool {

        $signatureHeader = $request->header(
            'Paymongo-Signature'
        );

        $webhookSecret = config(
            'services.paymongo.webhook_secret'
        );

        if (!$signatureHeader || !$webhookSecret) {

            Log::warning(
                'PayMongo signature verification failed: missing credentials'
            );

            return false;
        }

        $parts = [];

        foreach (
            explode(',', $signatureHeader)
            as $pair
        ) {

            [$key, $value] = array_pad(
                explode('=', trim($pair), 2),
                2,
                null
            );

            if ($key && $value) {
                $parts[$key] = $value;
            }
        }

        $timestamp = $parts['t'] ?? null;

        $expectedSignature =
            $parts['te']
            ?? $parts['li']
            ?? null;

        if (!$timestamp || !$expectedSignature) {

            Log::warning(
                'PayMongo signature verification failed: invalid header'
            );

            return false;
        }

        $signedPayload =
            $timestamp . '.' . $rawPayload;

        $computedSignature = hash_hmac(
            'sha256',
            $signedPayload,
            $webhookSecret
        );

        return hash_equals(
            $expectedSignature,
            $computedSignature
        );
    }

    private function extractBookingCode(
        array $checkoutSession
    ): ?string {

        $successUrl = $checkoutSession['success_url'] ?? null;

        if (!$successUrl) {
            return null;
        }

        $query = parse_url(
            $successUrl,
            PHP_URL_QUERY
        );

        if (!$query) {
            return null;
        }

        parse_str(
            $query,
            $params
        );

        return $params['booking_code'] ?? null;
    }
}
