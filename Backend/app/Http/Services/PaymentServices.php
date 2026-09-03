<?php

namespace App\Http\Services;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\SubmittedPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentServices
{

    public function attemptCreatePayment(Request $request)
    {
        try {

            $validation = $request->validate([
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'payment_type' => ['nullable']
            ]);

            $payment = Payment::create($validation);

            return response()->json([
                'message' => 'Payment method created successfully.',
                'data' => $payment,
                'status' => 200,
            ], 200);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'message' => $th->getMessage(),
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function attemptGetPaymentMethod(Request $request)
    {
        try {
            $booking = Booking::with(['court'])->where('booking_code', $request->bookingCode)->first();
            $paymentMethods = Payment::with('user')->where('user_id', $booking->user_id)->get();
            $paymentType = Payment::where('user_id', $booking->user_id)->pluck('payment_type');
            $paymentImage = Payment::where('user_id', $booking->user_id)->pluck('image', 'payment_type');

            $startHour = Carbon::parse($booking->start_time)->hour;

            if ($startHour >= 6 && $startHour < 17) {
                $downpayment = 200;
            } else {
                $totalCost = $booking->court->price * $booking->hours;
                $downpayment = $totalCost * 0.5;
            }

            // $totalCost = $hourlyPrice * $booking->hours;
            // $downpayment = $totalCost * 0.5;

            // $downpayment = $booking->hours <= 2 ? ($booking->court->price * .5) : ($booking->court->price * 1);

            $reservation = [
                'label' => $booking->court->name,
                'amount' => $downpayment,
            ];

            return response()->json([
                'message' => 'Get list of payment Methods.',
                'data' => $paymentMethods,
                'types' => $paymentType,
                'image' => $paymentImage,
                'booking_id' => $booking->id,
                'ispaid' => $booking->payment_status,
                'reservations' => $reservation,
                'status' => 200,
            ], 200);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'message' => $th->getMessage(),
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function attemptSubmitPayment(Request $request)
    {
        try {
            $validation = $request->validate([
                'payment_id' => ['required', 'integer', 'exists:payments,id'],
                'booking_id' => ['required', 'integer', 'exists:bookings,id'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);

            if ($request->hasFile('image')) {
                $validation['image'] = $request->file('image')
                    ->store('submitted_payment', 'public');
            }

            $submittedPayment = SubmittedPayment::create($validation);

            Booking::where('booking_code', $request->booking_code)->update([
                'payment_status' => 'paid',
                'status' => 'confirmed'
            ]);

            return response()->json([
                'message' => 'Successfully submitted payment.',
                'data' => $submittedPayment,
                'status' => 200,
            ], 200);
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
