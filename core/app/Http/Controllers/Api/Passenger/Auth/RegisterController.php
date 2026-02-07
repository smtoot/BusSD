<?php

namespace App\Http\Controllers\Api\Passenger\Auth;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RegisterController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:40',
            'lastname'  => 'required|string|max:40',
            'email'     => 'required|email|unique:passengers',
            'mobile'    => 'required|string|unique:passengers',
            'dial_code' => 'required|string',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $passenger = Passenger::create([
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'username'  => $request->email, // Use email as username for now
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'dial_code' => $request->dial_code,
            'password'  => Hash::make($request->password),
            'status'    => 1,
            'ev'        => 0,
            'sv'        => 0,
            'phone_otp' => rand(100000, 999999),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send SMS OTP via local Sudanese gateway
        notify($passenger, 'SVER_CODE', [
            'code' => $passenger->phone_otp
        ]);

        $token = $passenger->createToken('auth_token')->plainTextToken;

        return $this->apiSuccess('Registration successful. Please verify your phone.', [
            'token' => $token,
            'passenger' => $passenger,
        ], 201);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $passenger = $request->user();

        if ($passenger->phone_otp !== $request->otp) {
            return $this->apiError('Invalid OTP.', 400);
        }

        if (Carbon::now()->gt($passenger->otp_expires_at)) {
            return $this->apiError('OTP has expired.', 400);
        }

        $passenger->sv = 1;
        $passenger->phone_otp = null;
        $passenger->otp_expires_at = null;
        $passenger->save();

        return $this->apiSuccess('Phone verified successfully.');
    }

    public function resendOtp(Request $request)
    {
        $passenger = $request->user();

        if ($passenger->sv == 1) {
            return $this->apiError('Phone already verified.', 400);
        }

        if ($passenger->otp_expires_at && Carbon::now()->lt($passenger->otp_expires_at->subMinutes(8))) {
            return $this->apiError('Please wait before requesting a new OTP.', 429);
        }

        $passenger->phone_otp = rand(100000, 999999);
        $passenger->otp_expires_at = Carbon::now()->addMinutes(10);
        $passenger->save();

        notify($passenger, 'SVER_CODE', [
            'code' => $passenger->phone_otp
        ]);

        return $this->apiSuccess('OTP has been resent to your phone.');
    }
}
