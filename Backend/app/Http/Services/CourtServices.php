<?php

namespace App\Http\Services;

use App\Models\Court;
use Illuminate\Http\Request;
use App\Models\CourtCloseTime;
use Illuminate\Validation\Rule;
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

            $courts = $query->get();

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
        try {
            $query = CourtCloseTime::query();

            $request->validate([
                'court_id' => ['nullable', 'integer', 'exists:courts,id'],
                'schedule' => ['nullable', 'date'],
            ]);

            if ($request->filled('court_id')) {
                $query->where('court_id', $request->integer('court_id'));
            }

            if ($request->filled('schedule')) {
                $query->whereDate('closed_date', $request->input('schedule'));
            }

            $courts = $query->first();

            return response()->json([
                'message' => 'Courts time close retrieved successfully.',
                'data' => $courts,
                'status' => 200,
            ]);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'message' => 'Something is wrong. Please try again.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }
}
