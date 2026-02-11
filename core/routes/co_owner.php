<?php

use Illuminate\Support\Facades\Route;

Route::namespace('CoOwner\Auth')->middleware('co-owner.guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('co-owner')->withoutMiddleware('co-owner.guest')->name('logout');
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

Route::middleware('co-owner')->namespace('CoOwner')->group(function () {
    Route::controller('CoOwnerController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');
        Route::get('manage-transport', 'manageTransport')->name('manage.transport');
        Route::get('chart/seals', 'salesReport')->name('chart.sales');
    });

    Route::middleware('check.plan:co-owner')->group(function () {
        Route::middleware('checkPermission:staff_management')->group(function () {
            Route::controller('SupervisorController')->prefix('supervisor')->name('supervisor.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'status')->name('status');
            });

            Route::controller('DriverController')->prefix('driver')->name('driver.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id?}', 'status')->name('status');
            });

            Route::controller('CounterController')->prefix('counter/manager')->name('counter.manager.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'status')->name('status');
            });
        });

        Route::middleware('checkPermission:fleet_management')->group(function () {
            Route::controller('FleetController')->group(function () {
                Route::prefix('seat-layouts')->name('seat.layout.')->group(function () {
                    Route::get('', 'seatLayout')->name('index');
                    Route::post('store/{id?}', 'layoutStore')->name('store');
                    Route::post('status/{id}', 'layoutStatus')->name('status');
                });

                route::prefix('fleet-type')->name('fleet.type.')->group(function () {
                    Route::get('', 'fleetType')->name('index');
                    Route::post('store/{id?}', 'fleetTypeStore')->name('store');
                    Route::post('status/{id}', 'fleetTypeStatus')->name('status');
                });

                route::prefix('vehicle')->name('vehicle.')->group(function () {
                    Route::get('', 'vehicle')->name('index');
                    Route::post('store/{id?}', 'vehicleStore')->name('store');
                    Route::post('status/{id}', 'changeVehicleStatus')->name('status');
                });
            });
        });

        Route::middleware('checkPermission:trip_management')->group(function () {
            Route::controller('CounterController')->prefix('counter')->name('counter.')->group(function () {
                Route::get('', 'counter')->name('index');
                Route::post('store/{id?}', 'counterStore')->name('store');
                Route::post('status/{id}', 'counterStatus')->name('status');
            });

            Route::controller('TripController')->prefix('trip')->name('trip.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'changeTripStatus')->name('status');

                Route::prefix('route')->name('route.')->group(function () {
                    Route::get('', 'route')->name('index');
                    Route::get('form/{id?}', 'routeForm')->name('form');
                    Route::post('store/{id?}', 'routeStore')->name('store');
                    Route::post('status/{id}', 'changeRouteStatus')->name('status');
                });

                Route::prefix('stoppage')->name('stoppage.')->group(function () {
                    Route::get('', 'stoppage')->name('index');
                    Route::get('trashed', 'stoppageTrashed')->name('trashed');
                    Route::post('create/{id}', 'stoppageStore')->name('store');
                    Route::post('remove/{id}', 'stoppageRemove')->name('remove');
                });

                Route::prefix('vehicles')->name('assign.vehicle.')->group(function () {
                    Route::get('', 'assignVehicle')->name('index');
                    Route::post('store/{id?}', 'assignVehicleStore')->name('store');
                    Route::post('status/{id}', 'changeAssignVehicleStatus')->name('status');
                });
            });

            Route::controller('ScheduleController')->prefix('trip/schedules')->name('trip.schedule.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'changeStatus')->name('status');
            });

            Route::controller('VehicleTicketController')->group(function () {
                Route::prefix('ticket/price')->name('trip.ticket.price.')->group(function () {
                    Route::get('', 'index')->name('index');
                    Route::get('create', 'create')->name('create');
                    Route::post('store', 'ticketPriceStore')->name('store');
                    Route::get('edit/{id}', 'edit')->name('edit');
                    Route::post('update/{id}', 'updatePrices')->name('update');
                    Route::post('status/{id}', 'changeTicketPriceStatus')->name('status');
                });

                Route::name('trip.ticket.')->group(function () {
                    Route::get('route_data', 'getRouteData')->name('get_route_data');
                    Route::get('check_price', 'checkTicketPrice')->name('check_price');
                });
            });
        });

        Route::middleware('checkPermission:sales_reports')->group(function () {
            Route::controller('SalesReportController')->group(function () {
                Route::name('report.sale.')->prefix('report/sale')->group(function () {
                    Route::get('', 'index')->name('index');
                    Route::get('{id}', 'saleDetail')->name('details');
                });
                Route::get('report/periodic/', 'periodic')->name('report.periodic');
            });
        });
    });
});
