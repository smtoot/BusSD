<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Manager\Auth')->middleware('manager.guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('manager')->withoutMiddleware('manager.guest')->name('logout');
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

Route::middleware('manager')->namespace('Manager')->group(function () {
    Route::controller('ManagerController')->group(function () {
        Route::get('sell', 'sell')->name('dashboard');
        Route::post('trip/search', 'searchTrip')->name('sell.search');
        Route::get('statistics', 'statistics')->name('statistics');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile/update', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password/update', 'passwordUpdate')->name('password.update');
    });

    Route::middleware(['check.plan:manager'])->group(function () {
        Route::middleware('checkPermission:booking_management')->group(function () {
            Route::controller('ManagerController')->group(function () {
                Route::get('sell/ticket/book/{ticketPriceId}/{id}', 'book')->name('sell.book');
                Route::get('sellbydate/ticket/book/{id}', 'bookByDate')->name('sell.book.bydate');
                Route::post('sell/ticket/book/{id}', 'booked')->name('sell.book.booked');
                Route::get('sell/ticket/print/{id}', 'ticketPrint')->name('sell.ticket.print');
                Route::get('trip/ticket/get-ticket-price', 'getTicketPrice')->name('ticket.get-price');
            });
        });

        Route::middleware('checkPermission:trip_management')->group(function () {
            Route::controller('ManagerController')->group(function () {
                Route::get('trips', 'trips')->name('trip.index');
            });
        });

        Route::middleware('checkPermission:sales_reports')->group(function () {
            Route::controller('ManagerController')->group(function () {
                Route::get('sold-tickets/todays', 'todaysSold')->name('sold.tickets.todays');
                Route::get('sold-tickets/alltime', 'allSold')->name('sold.tickets.all');
                Route::get('sold-tickets/canceled', 'cancelledSold')->name('sold.tickets.canceled');
                Route::post('sold-tickets/cancel/{id}', 'cancelSold')->name('sold.tickets.cancel');
            });
        });
    });
});
