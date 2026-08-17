<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymongoWebhookController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/list/venue', [VenueController::class, 'getList']);
Route::post('/list/court', [CourtController::class, 'getCourts']);
Route::get('/list/venue/closedate', [VenueController::class, 'getVenueCloseDate']);
Route::post('view/venue/closedate', [VenueController::class, 'getVenueCloseDateById']);
Route::post('/list/court/closeTime', [CourtController::class, 'courtCloseTime']);
Route::post('/create/booking', [BookingController::class, 'createBooking']);
Route::post('/view/booking', [BookingController::class, 'getBookingReservationByVenueId']);
Route::post('/check/booking', [BookingController::class, 'getCheckBookingReservation']);
Route::post('/update/booking/status', [BookingController::class, 'getBookingReservationChangeById']);
Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handle']);
Route::post('/view/venue/slugs', [VenueController::class, 'getVenueBySlug']);

Route::post('/create/user/newaccount', [UserController::class, 'createNewAccount']);

Route::get('/testingforbackend', function () {
    $users = User::get();
    return response()->json(['status' => 'success', 'message' => 'Testing successful', 'data' => $users]);
});

// For Payment Maya & Gcash
Route::post('/checkout', [PaymentController::class, 'createCheckout']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AuthController::class, 'users']);

    // Venues API Routes
    Route::post('/create/venue', [VenueController::class, 'createVenue']);
    Route::post('/admin/create/venue', [VenueController::class, 'setVenueAdmin']);
    Route::get('/admin/list/venue', [VenueController::class, 'getAdminVenue']);
    Route::post('/admin/create/venue/closedate', [VenueController::class, 'setVenueCloseDate']);
    Route::post('/admin/remove/venue/closedate', [VenueController::class, 'removeVenueCloseDate']);

    // Court API Routes
    Route::post('/admin/create/court', [CourtController::class, 'createCourt']);
    Route::post('/admin/create/court/closeTime', [CourtController::class, 'setCourtClosedTime']);

    //Booking API Routes
    Route::get('/admin/list/booking', [BookingController::class, 'getMyAccount']);
    Route::get('/admin/review/booking', [BookingController::class, 'getBookingByCode']);
    Route::post('/admin/view/booking', [BookingController::class, 'getBookingByVenueId']);
});
