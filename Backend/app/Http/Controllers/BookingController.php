<?php

namespace App\Http\Controllers;

use App\Http\Services\BookingServices;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private $bookingServices;

    public function __construct(BookingServices $bookingServices)
    {
        $this->bookingServices = $bookingServices;
    }

    public function createBooking(Request $request)
    {
        return $this->bookingServices->attemptCreateBooking($request);
    }

    public function getBookingByCode(Request $request)
    {
        return $this->bookingServices->attemptGetBookingByCode($request);
    }

    public function getMyAccount(Request $request)
    {
        return $this->bookingServices->attemptGetMyAccount($request);
    }

    public function getBookingByVenueId(Request $request){
        return $this->bookingServices->attemptGetBookingByVenueId($request);
    }

    public function getBookingReservationByVenueId(Request $request){
        return $this->bookingServices->getBookingReservationByVenueId($request);
    }

    public function getBookingReservationChangeById(Request $request){
        return $this->bookingServices->attemptGetBookingReservationChangeById($request);
    }
    public function getCheckBookingReservation(Request $request){
        return $this->bookingServices->attemptGetBookingByCodeOrPhone($request);
    }
}
