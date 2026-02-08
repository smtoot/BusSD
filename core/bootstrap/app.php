<?php

use App\Http\Middleware\Demo;
use Laramin\Utility\VugiChugi;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckPackage;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RedirectIfOwner;
use App\Http\Middleware\OwnerCheckStatus;
use App\Http\Middleware\RedirectIfDriver;
use App\Http\Middleware\RegistrationStep;
use App\Http\Middleware\RedirectIfCoOwner;
use App\Http\Middleware\RedirectIfManager;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RedirectIfNotOwner;
use App\Http\Middleware\RedirectIfNotDriver;
use App\Http\Middleware\RedirectIfNotCoOwner;
use App\Http\Middleware\RedirectIfNotManager;
use App\Http\Middleware\RedirectIfSupervisor;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfNotSupervisor;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {

            Route::namespace('App\Http\Controllers')->group(function () {
                Route::middleware(['web'])
                    ->namespace('Admin')
                    ->prefix('admin')
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));

                Route::middleware(['web', 'maintenance'])
                    ->namespace('Gateway')
                    ->prefix('ipn')
                    ->name('ipn.')
                    ->group(base_path('routes/ipn.php'));

                Route::middleware(['web', 'maintenance'])
                    ->prefix('owner')
                    ->group(base_path('routes/owner.php'));

                Route::middleware(['web', 'maintenance'])
                    ->prefix('co-owner')
                    ->name('co-owner.')
                    ->group(base_path('routes/co_owner.php'));

                Route::middleware(['web', 'maintenance'])
                    ->prefix('manager')
                    ->name('manager.')
                    ->group(base_path('routes/manager.php'));

                Route::middleware(['web', 'maintenance'])
                    ->prefix('driver')
                    ->name('driver.')
                    ->group(base_path('routes/driver.php'));

                Route::middleware(['web', 'maintenance'])
                    ->prefix('supervisor')
                    ->name('supervisor.')
                    ->group(base_path('routes/supervisor.php'));

                Route::middleware(['api'])
                    ->prefix('api/v1')
                    ->group(base_path('routes/api.php'));

                Route::middleware(['web', 'maintenance'])->group(base_path('routes/web.php'));
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LanguageMiddleware::class,
            \App\Http\Middleware\ActiveTemplateMiddleware::class,
        ]);

        $middleware->alias([
            'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'              => \Illuminate\Auth\Middleware\Authorize::class,
            'auth'             => Authenticate::class,
            'guest'            => RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed'           => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            'admin'       => RedirectIfNotAdmin::class,
            'admin.guest' => RedirectIfAdmin::class,

            'owner'              => RedirectIfNotOwner::class,
            'owner.guest'        => RedirectIfOwner::class,
            'owner.check.status' => OwnerCheckStatus::class,
            'check.plan'         => CheckPackage::class,

            'co-owner'       => RedirectIfNotCoOwner::class,
            'co-owner.guest' => RedirectIfCoOwner::class,

            'manager'       => RedirectIfNotManager::class,
            'manager.guest' => RedirectIfManager::class,

            'driver'       => RedirectIfNotDriver::class,
            'driver.guest' => RedirectIfDriver::class,

            'supervisor'       => RedirectIfNotSupervisor::class,
            'supervisor.guest' => RedirectIfSupervisor::class,

            'check.status'          => CheckStatus::class,
            'demo'                  => Demo::class,
            'registration.complete' => RegistrationStep::class,
            'maintenance'           => MaintenanceMode::class,
        ]);

        $middleware->validateCsrfTokens(
            except: ['user/deposit', 'ipn*']
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function () {
            if (request()->is('api/*')) {
                return true;
            }
        });
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 401) {
                if (request()->is('api/*')) {
                    $notify[] = 'Unauthorized request';
                    return response()->json([
                        'remark' => 'unauthenticated',
                        'status' => 'error',
                        'message' => ['error' => $notify]
                    ]);
                }
            }

            if ($response->getStatusCode() === 429) {
                if (request()->is('api/*')) {
                    $notify[] = 'Too many requests. Please try again in a minute.';
                    return response()->json([
                        'remark' => 'too_many_requests',
                        'status' => 'error',
                        'message' => ['error' => $notify]
                    ]);
                }
            }

            return $response;
        });
    })->create();
