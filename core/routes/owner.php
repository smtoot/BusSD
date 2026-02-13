<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Owner\Auth')->name('owner.')->middleware('owner.guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('owner')->withoutMiddleware('owner.guest')->name('logout');
    });

    Route::controller('RegisterController')->middleware(['owner.guest'])->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('owner.guest');
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

Route::middleware('owner')->name('owner.')->group(function () {
    Route::namespace('Owner')->group(function () {
        Route::get('user-data', 'OwnerController@userData')->name('data');
        Route::post('user-data-submit', 'OwnerController@userDataSubmit')->name('data.submit');

        //authorization
        Route::middleware('registration.complete')->controller('AuthorizationController')->group(function () {
            Route::get('authorization', 'authorizeForm')->name('authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
            Route::post('verify-email', 'emailVerification')->name('verify.email');
            Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        });
    });

    Route::middleware(['owner.check.status', 'registration.complete'])->group(function () {
        Route::namespace('Owner')->group(function () {
            Route::controller('OwnerController')->group(function () {
                Route::get('dashboard', 'dashboard')->name('dashboard');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
                Route::get('profile', 'profile')->name('profile');
                Route::post('profile', 'profileUpdate')->name('profile.update');
                Route::get('password', 'password')->name('password');
                Route::post('password', 'passwordUpdate')->name('password.update');

                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');

                Route::get('chart/seals', 'salesReport')->name('chart.sales');
                Route::get('notifications/app-bookings', 'recentAppBookings')->name('notifications.app');

                // package
                Route::prefix('package')->name('package.')->group(function () {
                    Route::get('', 'package')->name('index');
                    Route::get('purchased', 'packageActive')->name('active');
                    Route::post('buy/{id}', 'packageBuy')->name('buy');
                });

                Route::any('deposit/history', 'depositHistory')->name('deposit.history');

                //Settings
                Route::prefix('settings')->name('settings.')->group(function () {
                    Route::get('', 'settings')->name('index');
                    Route::post('store', 'store')->name('store');
                });

                Route::get('manage-transport', 'manageTransport')->name('manage.transport');
            });

            Route::controller('OwnerController')->middleware('check.plan')->group(function () {
                Route::get('general-setting', 'generalSettings')->name('general.setting');
                Route::post('general-setting', 'generalSettingsUpdate')->name('general.setting.update');
            });

            // Support Ticket
            Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
                Route::get('', 'supportTicket')->name('index');
                Route::get('new', 'openSupportTicket')->name('open');
                Route::post('store', 'storeSupportTicket')->name('store');
                Route::get('view/{ticket}', 'viewTicket')->name('view');
                Route::post('close/{id}', 'closeTicket')->name('close');
                Route::post('reply/{ticket}', 'replyTicket')->name('reply');
                Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
            });

            //CoOwner
            Route::controller('CoOwnerController')->prefix('co-owner')->name('co-owner.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'status')->name('status');
                Route::get('login/{id}', 'login')->name('login');
            });

            //Supervisor
            Route::controller('SupervisorController')->prefix('supervisor')->name('supervisor.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'status')->name('status');
                Route::get('login/{id}', 'login')->name('login');
            });

            //Driver
            Route::controller('DriverController')->prefix('driver')->name('driver.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id?}', 'status')->name('status');
                Route::get('login/{id}', 'login')->name('login');
            });

            //Counter Manager
            Route::controller('BranchController')->prefix('counter/manager')->name('counter.manager.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'status')->name('status');
                Route::get('login/{id}', 'login')->name('login');
            });

            //Branches (formerly Counters)
            Route::controller('BranchController')->prefix('counter')->name('counter.')->group(function () {
                Route::get('', 'counter')->name('index');
                Route::get('create', 'create')->name('create');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('store', 'counterStore')->name('store');
                Route::post('update/{id}', 'counterUpdate')->name('update');
                Route::post('status/{id}', 'counterStatus')->name('status');
                Route::get('login/{id}', 'login')->name('login');
            });

            //Fleets Manage
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
                    Route::get('create', 'vehicleCreate')->name('create');
                    Route::get('edit/{id}', 'vehicleEdit')->name('edit');
                    Route::post('store/{id?}', 'vehicleStore')->name('store');
                    Route::post('status/{id}', 'changeVehicleStatus')->name('status');
                });
            });
            
            //Bookings Manage
            Route::controller('BookingController')->prefix('bookings')->name('bookings.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('manage/{id}', 'manage')->name('manage');
                Route::post('check-in/{id}', 'checkin')->name('checkin');
            });
            
            //Trip Manage
            Route::controller('TripController')->prefix('trip')->name('trip.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('form/{id?}', 'form')->name('form');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'changeTripStatus')->name('status');

                // New API endpoints for redBus features
                Route::get('pricing-preview', 'getPricingPreview')->name('pricing.preview');
                Route::get('available-vehicles', 'getAvailableVehicles')->name('available.vehicles');
                Route::get('show/{id}', 'show')->name('show');

                //Trip Manage - Stoppage
                Route::prefix('stoppage')->name('stoppage.')->group(function () {
                    Route::get('', 'stoppage')->name('index');
                    Route::get('trashed', 'stoppageTrashed')->name('trashed');
                    Route::post('create/{id}', 'stoppageStore')->name('store');
                    Route::post('remove/{id}', 'stoppageRemove')->name('remove');
                });

                //Trip Management - Bus Assign
                Route::prefix('vehicles')->name('assign.vehicle.')->group(function () {
                    Route::get('', 'assignVehicle')->name('index');
                    Route::post('store/{id?}', 'assignVehicleStore')->name('store');
                    Route::post('status/{id}', 'changeAssignVehicleStatus')->name('status');
                });

                // Pricing Management (Phase 2.1 - Unified Pricing Engine)
                Route::controller('TripPricingController')->prefix('{trip}/pricing')->name('pricing.')->group(function () {
                    Route::get('preview', 'preview')->name('preview');
                    Route::get('suggest', 'suggest')->name('suggest');
                    Route::get('rules', 'rules')->name('rules');
                });
            });

            // Seat Pricing Management (Phase 2.2 - Multi-Tier Seat Pricing)
            Route::controller('SeatPricingController')->prefix('seat-pricing')->name('seat.pricing.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/form/{id?}', 'form')->name('form');
                Route::post('/store/{id?}', 'store')->name('store');
                Route::post('/status/{id}', 'status')->name('status');
                Route::post('/delete/{id}', 'delete')->name('delete');
                Route::get('/preview/{tripId}', 'preview')->name('preview');
            });

            // Route Builder (Phase 2.3 - Visual Route Templates)
            Route::controller('RouteBuilderController')->prefix('route-builder')->name('route.builder.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/update/{id}', 'update')->name('update');
                Route::post('/status/{id}', 'status')->name('status');
                Route::post('/delete/{id}', 'delete')->name('delete');
                Route::get('/load/{id}', 'load')->name('load'); // AJAX
            });

            // Route Analytics (Phase 3.1 - Profitability Dashboard)
            Route::controller('RouteAnalyticsController')->prefix('analytics')->name('analytics.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/route/{id}', 'route')->name('route');
                Route::post('/compare', 'compare')->name('compare'); // AJAX
                Route::get('/export/{id}', 'export')->name('export'); // AJAX
            });

            //Route Manage
            Route::controller('RouteController')->prefix('route')->name('route.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store/{id?}', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('status/{id}', 'status')->name('status');
            });

            //Trip Manage - Schedules
            Route::controller('ScheduleController')->prefix('trip-schedules')->name('trip.schedule.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('status/{id}', 'changeStatus')->name('status');
            });

            //Ticket Management
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

            //Boarding Points
            Route::controller('BoardingPointController')->prefix('boarding-points')->name('boarding-points.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::post('status/{id}', 'status')->name('status');
                Route::post('delete/{id}', 'delete')->name('delete');
                Route::get('assign/{routeId}', 'assign')->name('assign');
                Route::post('assign/{routeId}', 'assignStore')->name('assign.store');
            });

            //Dropping Points
            Route::controller('DroppingPointController')->prefix('dropping-points')->name('dropping-points.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::post('status/{id}', 'status')->name('status');
                Route::post('delete/{id}', 'delete')->name('delete');
                Route::get('assign/{routeId}', 'assign')->name('assign');
                Route::post('assign/{routeId}', 'assignStore')->name('assign.store');
            });



            //Report
            Route::controller('SalesReportController')->group(function () {
                Route::name('report.sale.')->prefix('report/sale')->group(function () {
                    Route::get('', 'index')->name('index');
                    Route::get('app', 'appSales')->name('app'); // New
                    Route::get('counter', 'counterSales')->name('counter'); // New
                    Route::get('{id}', 'saleDetail')->name('details');
                });
                Route::get('report/periodic/', 'periodic')->name('report.periodic');
                Route::get('report/performance/', 'performance')->name('report.performance');
            });

            // Passenger CRM
            Route::controller('PassengerController')->prefix('passengers')->name('passenger.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('history/{id}', 'viewHistory')->name('history');
            });

            // Feedback System
            Route::controller('TripRatingController')->prefix('feedback')->name('feedback.')->group(function () {
                Route::get('/', 'index')->name('index');
            });

            // Financial Transparency
            Route::controller('FinancialController')->prefix('financials')->name('financial.')->group(function () {
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('settlements', 'settlements')->name('settlements');
                Route::get('refunds', 'refunds')->name('refunds');
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw.')->group(function () {
                Route::get('/', 'withdrawMoney')->name('methods');
                Route::post('money', 'withdrawStore')->name('money');
                Route::get('history', 'withdrawLog')->name('history');
            });
        });

        // Payment
        Route::prefix('payment')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});
