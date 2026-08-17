<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserServices
{
    public function attemptCreateNewAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:super_admin,sub_admin'],
            'is_admin' => ['nullable', 'boolean'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        try {

            if ($this->password() !== $request->adminPassword) {
                return response()->json([
                    'message' => 'Not bad. Shiro Admin Required Password. Do not try again.',
                    'data' => [],
                    'status' => 402,
                ]);
            }

            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('profiles', 'public');
            }

            $newAccount = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'is_admin' => $validated['is_admin'] ?? false,
                'password' => Hash::make($validated['password']),
                'image' => $imagePath,
            ]);

            return response()->json([
                'message' => 'Account created successfully.',
                'data' => [
                    'id' => $newAccount->id,
                    'name' => $newAccount->name,
                    'email' => $newAccount->email,
                    'image' => $newAccount->image,
                ],
                'status' => 201,
            ], 201);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'message' => $th->getMessage(),
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    private function password()
    {
        return 'ShiroP@ssword';
    }
}
