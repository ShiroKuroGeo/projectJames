<?php

namespace App\Http\Controllers;

use App\Http\Services\CourtServices;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    private $courtServices;

    public function __construct(CourtServices $courtServices)
    {
        $this->courtServices = $courtServices;
    }

    public function createCourt(Request $request){
        return $this->courtServices->attemptCreateCourt($request);
    }

    public function getCourts(Request $request){
        return $this->courtServices->attemptGetCourts($request);
    }

    public function setCourtClosedTime(Request $request){
        return $this->courtServices->attemptSetCourtClosedTime($request);
    }

    public function courtCloseTime(Request $request){
        return $this->courtServices->attemptCourtCloseTime($request);
    }

}
