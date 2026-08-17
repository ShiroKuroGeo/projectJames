<?php

namespace App\Http\Controllers;

use App\Http\Services\UserServices;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }


    public function createNewAccount(Request $request){
        return $this->userServices->attemptCreateNewAccount($request);
    }
}
