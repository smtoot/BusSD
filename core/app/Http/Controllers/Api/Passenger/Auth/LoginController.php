<?php

namespace App\Http\Controllers\Api\Passenger\Auth;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required', // Can be email or mobile
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $passenger = Passenger::where('email', $request->username)
                              ->orWhere('mobile', $request->username)
                              ->first();

        if (!$passenger || !Hash::check($request->password, $passenger->password)) {
            return $this->apiError('Invalid credentials.', 401);
        }

        if ($passenger->status == 0) {
            return $this->apiError('Your account has been banned.', 403);
        }

        $token = $passenger->createToken('auth_token')->plainTextToken;

        return $this->apiSuccess('Login successful.', [
            'token' => $token,
            'passenger' => $passenger,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->apiSuccess('Logged out successfully.');
    }
}
