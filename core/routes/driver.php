<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Driver\Auth')->middleware('driver.guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('driver')->withoutMiddleware('driver.guest')->name('logout');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
        Route::post('password/reset', 'reset')->name('password.update');
    });
});

Route::middleware('driver')->namespace('Driver')->group(function () {
    Route::controller('DriverController')->group(function(){
        Route::get('trip', 'dashboard')->name('dashboard');
        Route::get('trip/view/{id}', 'viewTrips')->name('trips.view');

        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');
        Route::middleware('checkPermission:trip_management')->group(function () {
            Route::get('trips', 'trips')->name('trips');
        });
    });
});
