<?php

namespace IFP\SaleAdvertSearch;

use Illuminate\Support\ServiceProvider;

class SaleAdvertSearchClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require base_path('vendor/ifp/sale-advert-search-client/src/routes.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
