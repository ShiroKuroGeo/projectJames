<?php

namespace App\Http\Services;

use App\Models\Court;
use Illuminate\Http\Request;
use App\Models\CourtCloseTime;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class CourtServices
{

    public function attemptCreateCourt(Request $request)
    {
        try {
            $validated = $request->validate([
                'venue_id'          => ['required', 'integer', 'exists:venues,id'],
                'name'              => ['required', 'string', 'max:255'],
                'tag'   => ['required', 'array', 'min:1'],
                'tag.*' => ['required', 'string', 'max:255'],
                'price'             => ['required', 'numeric', 'min:0'],
                'price_definition'  => ['required', 'string', 'max:255'],
            ]);

            $court = Court::create($validated);

            return response()->json([
                'message' => 'Court created successfully.',
                'data' => $court,
                'status' => 201,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
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

    public function attemptGetCourts(Request $request)
    {
        try {
            $query = Court::query();

            if ($request->filled('venue_id')) {
                $request->validate([
                    'venue_id' => ['integer', 'exists:venues,id'],
                ]);

                $query->where('venue_id', $request->integer('venue_id'));
            }

            // $courts = $query->get();
            $courts = $query->orderBy('created_at', 'asc')->get();

            return response()->json([
                'message' => 'Courts retrieved successfully.',
                'data' => $courts,
                'status' => 200,
            ]);
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

    public function attemptSetCourtClosedTime(Request $request)
    {
        try {
            $validated = $request->validate([
                'court_id' => ['required', 'integer', 'exists:courts,id'],
                'closed_date' => ['required', 'date'],
                'closed_times' => ['required', 'array', 'min:1'],
                'closed_times.*' => ['string', Rule::in([
                    '12:00 AM',
                    '01:00 AM',
                    '02:00 AM',
                    '03:00 AM',
                    '04:00 AM',
                    '05:00 AM',
                    '06:00 AM',
                    '07:00 AM',
                    '08:00 AM',
                    '09:00 AM',
                    '10:00 AM',
                    '11:00 AM',
                    '12:00 PM',
                    '01:00 PM',
                    '02:00 PM',
                    '03:00 PM',
                    '04:00 PM',
                    '05:00 PM',
                    '06:00 PM',
                    '07:00 PM',
                    '08:00 PM',
                    '09:00 PM',
                    '10:00 PM',
                    '11:00 PM',
                ])],
            ]);

            $closedTimes = array_values(array_unique($validated['closed_times']));

            CourtCloseTime::updateOrCreate(
                [
                    'court_id' => $validated['court_id'],
                    'closed_date' => $validated['closed_date'],
                ],
                [
                    'closed_times' => $closedTimes,
                ]
            );

            return response()->json([
                'message' => 'Court closed times successfully set.',
                'data' => [],
                'status' => 201,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
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

    public function attemptCourtCloseTime(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'integer', 'exists:courts,id'],
            'schedule' => ['required', 'date'],
        ]);

        try {
            $date = Carbon::parse($validated['schedule'])->toDateString();

            $closedTimes = CourtCloseTime::where('court_id', $validated['court_id'])
                ->whereDate('closed_date', $date)
                ->get()
                ->flatMap(function ($row) {
                    $times = $row->closed_times;

                    while (is_string($times)) {
                        $decoded = json_decode($times, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            break;
                        }
                        $times = $decoded;
                    }

                    return Arr::flatten((array) $times);
                })
                ->filter(fn($t) => is_string($t) && $t !== '')
                ->map(fn($time) => Carbon::parse($time)->format('H:i'))
                ->unique()
                ->sort()
                ->map(fn($time) => Carbon::createFromFormat('H:i', $time)->format('h:i A'))
                ->values();

            return response()->json([
                'message' => 'Successfully retrieved court close times.',
                'data'    => $closedTimes,
                'status'  => 200,
            ], 200);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                // 'message' => 'Something went wrong. Please try again.',
                'message' => $th->getMessage(),
                'error'   => config('app.debug') ? $th->getMessage() : null,
                'status'  => 500,
            ], 500);
        }
    }
}
