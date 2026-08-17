<?php

namespace App\Http\Controllers;

use App\Http\Services\VenueServices;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public $venue;

    public function __construct(VenueServices $venueServices)
    {
        $this->venue = $venueServices;
    }

    public function createVenue(Request $request){
        return $this->venue->attemptCreateVenue($request);
    }

    public function getList(){
        return $this->venue->attemptGetList();
    }

    public function setVenueAdmin(Request $request){
        return $this->venue->attemptSetVenueAdmin($request);
    }

    public function getAdminVenue(){
        return $this->venue->attemptGetAdminVenue();
    } 

    public function setVenueCloseDate(Request $request){
        return $this->venue->attemptSetVenueCloseDate($request);
    }

    public function removeVenueCloseDate(Request $request){
        return $this->venue->attemptremoveVenueCloseDate($request);
    }

    public function getVenueCloseDate(Request $request){
        return $this->venue->attemptGetVenueCloseDate($request);
    }

    public function getVenueCloseDateById(Request $request){
        return $this->venue->attemptGetVenueCloseDateById($request);
    }

    public function getVenueBySlug(Request $request){
        return $this->venue->attemptGetVenueBySlug($request);
    }
}
