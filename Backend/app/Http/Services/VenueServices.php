<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Venue;
use App\Models\VenueAdmin;
use App\Models\VenueClosedDates;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VenueServices
{
    public function attemptCreateVenue(Request $request)
    {
        try {
            $validated = $request->validate([
                'slugs'        => ['required', 'string', 'max:255', 'unique:venues,slugs'],
                'name'         => ['required', 'string', 'max:255'],
                'area'         => ['required', 'string', 'max:255'],
                'latitude'     => ['required', 'numeric', 'between:-90,90'],
                'longitude'    => ['required', 'numeric', 'between:-180,180'],
                'is_featured' => ['nullable', 'boolean'],
                'gcash_number' => ['nullable', 'string', 'max:20'],
                'maya_number'  => ['nullable', 'string', 'max:20'],
            ]);

            $venue = Venue::create($validated);

            return response()->json([
                'message' => 'Venue successfully created.',
                'data' => $venue,
                'status' => 200,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'A venue with this slug already exists.',
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

    public function attemptGetList()
    {
        try {
            return response()->json([
                'message' => 'Venue list successfully retrieved.',
                'data' => VenueAdmin::with(['user', 'venue.courts'])->get(),
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

    public function attemptSetVenueAdmin(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'venue_id' => ['required', 'integer', 'exists:venues,id'],
            ]);

            VenueAdmin::create([
                'user_id' => $validated['user_id'],
                'venue_id' => $validated['venue_id'],
            ]);

            return response()->json([
                'message' => 'Venue admin successfully added.',
                'data' => [],
                'status' => 201,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'This user is already an admin for this venue.',
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

    public function attemptGetAdminVenue()
    {
        try {
            $user = auth()->user();

            return response()->json([
                'message' => 'Successfully retrieved venues.',
                'data' => $user->venues,
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

    public function attemptSetVenueCloseDate(Request $request)
    {
        try {
            $validated = $request->validate([
                'venue_id' => ['required', 'integer', 'exists:venues,id'],
                'closed_date' => ['required', 'date'],
                'reason' => ['nullable', 'string'],
            ]);

            $create = VenueClosedDates::create([
                'venue_id' => $validated['venue_id'],
                'closed_date' => $validated['closed_date'],
                'reason' => $validated['reason'] ?? null,
            ]);

            return response()->json([
                'message' => 'Venue closing date successfully set.',
                'data' => $create->id,
                'status' => 200,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'This venue already has a closed date set for this day.',
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

    public function attemptremoveVenueCloseDate(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => ['required', 'integer'],
            ]);

            VenueClosedDates::where('id', $validated['id'])->delete();

            return response()->json([
                'message' => 'Venue closing date successfully removed.',
                'data' => [],
                'status' => 200,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'This venue already has a closed date remove for this day.',
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

    public function attemptGetVenueCloseDate(Request $request)
    {
        try {
            $user = auth()->user();

            $venueIds = $user->venues()->pluck('venues.id');

            $closedDates = VenueClosedDates::whereIn('venue_id', $venueIds)->get();

            return response()->json([
                'message' => 'Successfully retrieved venue closed dates.',
                'data' => $closedDates,
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

    public function attemptGetVenueCloseDateById(Request $request)
    {
        try {

            $validated = $request->validate([
                'venue_id' => ['required', 'integer', 'exists:venues,id']
            ]);

            $closedDates = VenueClosedDates::where('venue_id', $validated['venue_id'])->get();

            return response()->json([
                'message' => 'Successfully retrieved venue closed dates.',
                'data' => $closedDates,
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

    public function attemptGetVenueBySlug(Request $request)
    {
        try {
            $validated = $request->validate([
                'slugs' => ['required', 'exists:venues,slugs']
            ]);

            $venueBySlug = Venue::with(['admins', 'courts'])->where('slugs', $validated['slugs'])->first();

            return response()->json([
                'message' => 'Successfully retrieved venue by slugs.',
                'data' => $venueBySlug,
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
}
