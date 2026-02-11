<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laramin\Utility\VugiChugi;
use Laramin\Utility\Controller\UtilityController;

class CustomUtilityServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $router = $this->app['router'];

        // Define 'gotocore' middleware group (empty) so routes.php doesn't fail
        $router->middlewareGroup(VugiChugi::gtc(), []);
        
        // Define 'checkProject' middleware group (empty) just in case it is used somewhere
        $router->middlewareGroup(VugiChugi::mdNm(), []);

        // Load the routes from the vendor package
        // We load them so route() helpers don't break, but without the nasty middleware
        $this->loadRoutesFrom(base_path('vendor/laramin/utility/src/routes.php'));

        // Load views
        $this->loadViewsFrom(base_path('vendor/laramin/utility/src/Views'), 'Utility');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
