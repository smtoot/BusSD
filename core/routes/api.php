<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Passenger\Auth\RegisterController;
use App\Http\Controllers\Api\Passenger\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::middleware('throttle:api')->group(function () {
    Route::controller(RegisterController::class)->group(function () {
        Route::post('register', 'register')->middleware('throttle:auth');
    });

    Route::controller(LoginController::class)->group(function () {
        Route::post('login', 'login')->middleware('throttle:auth');
    });

    Route::controller(\App\Http\Controllers\Api\Passenger\TripSearchController::class)->group(function () {
        Route::get('locations', 'locations');
        Route::get('search', 'search');
        Route::get('trip/{id}/layout', 'layout');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('verify-otp', [RegisterController::class, 'verifyOtp'])->middleware('throttle:auth');
        Route::post('resend-otp', [RegisterController::class, 'resendOtp'])->middleware('throttle:auth');
        Route::post('logout', [LoginController::class, 'logout']);

        Route::controller(\App\Http\Controllers\Api\Passenger\ProfileController::class)->group(function () {
            Route::get('passenger/profile', 'show');
            Route::post('passenger/profile', 'update');
        });

        Route::controller(\App\Http\Controllers\Api\Passenger\BookingController::class)->group(function () {
            Route::post('booking/initiate', 'initiate')->middleware('throttle:booking');
            Route::get('passenger/trips/upcoming', 'upcoming');
            Route::get('passenger/trips/history', 'history');
            Route::get('ticket/{id}/view', 'viewTicket');
            Route::post('ticket/{id}/cancel', 'cancelTicket');
            Route::post('ticket/{id}/rate', 'rateTrip');
        });

        Route::controller(\App\Http\Controllers\Api\Passenger\PaymentController::class)->group(function () {
            Route::get('payment/methods', 'methods');
            Route::post('payment/initiate', 'initiate')->middleware('throttle:booking');
            Route::post('payment/manual/confirm', 'manualPaymentConfirm')->middleware('throttle:booking');
        });
    });
});
