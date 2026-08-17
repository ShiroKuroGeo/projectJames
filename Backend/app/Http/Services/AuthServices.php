<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthServices
{
    public function attemptLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            $user = User::where('email', $credentials['email'])->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->JSONReturn(null, null, 'invalid Credintials, Please try again.', 401, '');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->JSONReturn($token, $user, 'Successfully logged in.', 200, $user->role);
        } catch (\Throwable $th) {
            return $this->JSONReturn(null, null, $th->getMessage(), 501, '');
        }
    }

    public function attemptLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->JSONReturn(null, null, 'Logging out.', 200, '');
    }

    public function attemptGetUser(){
        $user = User::get();

        return $this->JSONReturn(null, $user, 'Successfully retrieve users.', 200, '');
    }

    protected function JSONReturn($token = null, $data = null, string $message = 'Something is wrong', int $status = 200, string $role = '')
    {
        return response()->json([
            'token'   => $token ?? '',
            'data'    => $data ?? [],
            'message' => $message,
            'status'  => $status,
            'role'    => $role ?? '',
        ], $status);
    }
}
