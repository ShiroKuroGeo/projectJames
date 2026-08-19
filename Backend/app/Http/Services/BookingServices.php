<?php

namespace App\Http\Services;

use App\Models\Booking;
use App\Models\Court;
use App\Models\VenueAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingServices
{
    public function attemptCreateBooking(Request $request)
    {
        try {
            $times = [
                '12:00 AM',
                '1:00 AM',
                '2:00 AM',
                '3:00 AM',
                '4:00 AM',
                '5:00 AM',
                '6:00 AM',
                '7:00 AM',
                '8:00 AM',
                '9:00 AM',
                '10:00 AM',
                '11:00 AM',
                '12:00 PM',
                '1:00 PM',
                '2:00 PM',
                '3:00 PM',
                '4:00 PM',
                '5:00 PM',
                '6:00 PM',
                '7:00 PM',
                '8:00 PM',
                '9:00 PM',
                '10:00 PM',
                '11:00 PM',
            ];

            $validated = $request->validate([
                'booking_code'    => ['required', 'string', 'max:100', 'unique:bookings,booking_code'],
                'venue_id'        => ['required', 'integer', 'exists:venues,id'],
                'court_id'        => ['required', 'integer', 'exists:courts,id'],
                'customer_name'   => ['required', 'string', 'max:20'],
                'customer_phone'  => ['required', 'string', 'max:13'],
                'customer_email'  => ['nullable', 'email', 'max:255'],
                'booking_date'    => ['required', 'date'],
                'start_time'      => ['required', 'string', Rule::in($times)],
                'end_time'        => ['required', 'string', Rule::in($times)],
                'hours'           => ['required', 'numeric', 'min:1'],
                'amount'          => ['required', 'numeric', 'min:0'],
                'notes'           => ['nullable', 'string'],
            ]);

            $venueAdmin = VenueAdmin::where('venue_id', $validated['venue_id'])->first();

            $validated['user_id'] = $venueAdmin->user_id;

            $courtBelongsToVenue = Court::where('id', $validated['court_id'])
                ->where('venue_id', $validated['venue_id'])
                ->exists();

            if (!$courtBelongsToVenue) {
                return response()->json([
                    'message' => 'This court does not belong to the specified venue.',
                    'data' => [],
                    'status' => 422,
                ], 422);
            }

            $conflict = Booking::where('court_id', $validated['court_id'])
                ->where('booking_date', $validated['booking_date'])
                ->where('start_time', $validated['start_time'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($conflict) {
                return response()->json([
                    'message' => 'This time slot is already booked for this court.',
                    'data' => [],
                    'status' => 409,
                ], 409);
            }

            $booking = Booking::create([
                ...$validated,
                'payment_method' => 'gcash',
                'payment_status' => $validated['payment_status'] ?? 'pending',
                'status' => $validated['status'] ?? 'pending',
            ]);

            return response()->json([
                'message' => 'Booking successfully created.',
                'data' => $booking,
                'status' => 201,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'A booking with this code already exists.',
                    'data' => [],
                    'status' => 409,
                ], 409);
            }

            report($e);

            return response()->json([
                'message' => 'Something is wrong. Please try again.',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'message' => 'Something is wrong. Please try again.',
                'data' => $th->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function attempUpdateAfterPayment(Request $request)
    {

        try {

            Booking::where('booking_code', $request->booking_code)->update([
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'status' => 'confirmed',
            ]);

            return true;
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'A booking with this code already exists.',
                    'data' => [],
                    'status' => 409,
                ], 409);
            }

            report($e);

            return response()->json([
                'message' => 'Something is wrong. Please try again.',
                'data' => [],
                'status' => 500,
            ], 500);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'message' => 'Something is wrong. Please try again.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }


    public function attemptGetBookingByCode(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_code' => ['nullable', 'string', 'max:100'],
                'customer_phone' => ['required', 'string', 'max:13'],
            ]);

            $query = Booking::with(['user', 'venue', 'court'])
                ->where('customer_phone', $validated['customer_phone']);

            if (!empty($validated['booking_code'])) {
                $query->where('booking_code', $validated['booking_code']);
                $booking = $query->first();
            } else {
                $booking = $query->latest()->first();
            }

            if (! $booking) {
                return response()->json([
                    'message' => 'Booking not found.',
                    'data' => [],
                    'status' => 404,
                ], 404);
            }

            return response()->json([
                'message' => 'Successfully retrieved booking.',
                'data' => $booking,
                'status' => 200,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'message' => 'Something is wrong. Please try again.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function attemptGetMyAccount(Request $request)
    {
        try {
            $user = auth()->user();

            return response()->json([
                'message' => 'Successfully retrieved account.',
                'data' => $user,
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

    public function attemptGetBookingByVenueId(Request $request)
    {
        try {
            $viewBooking = Booking::with(['venue', 'court'])->where('venue_id', $request->id)->get();

            return response()->json([
                'message' => 'Successfully retrieved booking by Venues Id.',
                'data' => $viewBooking,
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


    public function getBookingReservationByVenueId(Request $request)
    {
        $request->validate([
            'venue_id' => 'required|integer|exists:venues,id',
            'booking_date' => 'required|date',
        ]);

        try {
            $bookings = Booking::where('venue_id', $request->venue_id)
                ->where('booking_date', $request->booking_date)
                ->where('court_id', $request->court_id)
                ->where('status', '!=', 'cancelled')
                ->get(['start_time', 'end_time']);

            $reservedTimes = $bookings
                ->flatMap(fn($booking) => $this->expandTimeRange($booking->start_time, $booking->end_time))
                ->unique()
                ->values();

            return response()->json([
                'message' => 'Successfully retrieved reservation time booking by Venues Id.',
                'data' => $reservedTimes,
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

    public function attemptGetBookingReservationChangeById(Request $request)
    {
        try {
            Booking::where('id', $request->id)->update([
                'status' => $request->status
            ]);

            return response()->json([
                'message' => 'Successfully update status booking by Venues Id.',
                'data' => [],
                'status' => true,
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

    public function attemptGetBookingByCodeOrPhone(Request $request)
    {
        $request->validate([
            'booking_code' => 'nullable|string|required_without:customer_phone',
            'customer_phone' => 'nullable|string|required_without:booking_code',
        ]);

        try {
            $viewBooking = Booking::with(['venue', 'court'])
                ->when($request->filled('booking_code'), function ($q) use ($request) {
                    $q->where('booking_code', $request->booking_code);
                })
                ->when($request->filled('customer_phone'), function ($q) use ($request) {
                    $q->where('customer_phone', $request->customer_phone);
                })
                ->get();

            return response()->json([
                'message' => 'Successfully retrieved booking.',
                'data' => $viewBooking,
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

    private function expandTimeRange(string $start, string $end): array
    {
        $cursor = Carbon::createFromFormat('g:i A', $start);
        $endTime = Carbon::createFromFormat('g:i A', $end);

        if ($endTime->lt($cursor)) {
            $endTime->addDay();
        }

        $slots = [];
        while ($cursor->lte($endTime)) {
            $slots[] = $cursor->format('g:i A');
            $cursor->addHour();
        }

        return $slots;
    }
}
