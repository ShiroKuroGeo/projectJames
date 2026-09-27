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
            $request->validate([
                'bookingCode' => ['required', 'string'],
            ]);

            $booking = Booking::with(['court', 'venue'])
                ->where('booking_code', $request->bookingCode)
                ->first();

            if (!$booking) {
                return response()->json([
                    'message' => 'Booking not found.',
                    'data' => [],
                    'status' => 404,
                ], 404);
            }

            $paymentMethods = Payment::with('user')->where('user_id', $booking->user_id)->get();
            $paymentType = Payment::where('user_id', $booking->user_id)->pluck('payment_type');
            $paymentImage = Payment::where('user_id', $booking->user_id)->pluck('image', 'payment_type');

            $start = Carbon::parse($booking->start_datetime);
            $end = Carbon::parse($booking->end_datetime);

            $price = $booking->court->price;
            $specialRate = 200;
            $specialStartHour = 5;
            $specialEndHour = 17;

            $totalCost = 0;
            $cursor = $start->copy();

            while ($cursor->lt($end)) {
                $hourOfDay = (int) $cursor->format('G');
                $isSpecialHour = $hourOfDay >= $specialStartHour && $hourOfDay < $specialEndHour;
                $totalCost += $isSpecialHour ? $specialRate : $price;
                $cursor->addHour();
            }

            $downpayment = $totalCost * 0.5;

            $startHour = (int) $start->format('G');
            $courtPrice = ($startHour >= $specialStartHour && $startHour < $specialEndHour)
                ? $specialRate
                : $price;

            $reservation = [
                'label' => $booking->court->name,
                'amount' => $downpayment,
                'start_time' => $booking->start_datetime,
                'end_time' => $booking->end_datetime,
                'court' => $booking->court->name,
                'court_price' => $courtPrice,
                'venues' => $booking->venue->name,
                'location' => $booking->venue->area,
                'hours' => $booking->hours,
                'customer_name' => $booking->customer_name,
            ];

            $reservation = [
                'label' => $booking->court->name,
                'amount' => $downpayment,
                'start_time' => $booking->start_datetime,
                'end_time' => $booking->end_datetime,
                'court' => $booking->court->name,
                'court_price' => $courtPrice,
                'venues' => $booking->venue->name,
                'location' => $booking->venue->area,
                'hours' => $booking->hours,
                'customer_name' => $booking->customer_name,
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
                'message' => 'Something is wrong. Please try again.',
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
