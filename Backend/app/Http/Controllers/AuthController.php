<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AuthServices;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthServices $authService)
    {
        $this->authService = $authService;
    }
    
    public function login(Request $request){
        $stmt = $this->authService->attemptLogin($request);

        return $stmt;
    }

    public function logout(Request $request){
        return $this->authService->attemptLogout($request);
    }

    public function users(){
        return $this->authService->attemptGetUser();
    }

}
