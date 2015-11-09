<?php

namespace IFP\SaleAdvertSearch;

use Illuminate\Support\ServiceProvider;

class SaleAdvertSearchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require base_path('vendor/ifp/sale-advert-search/src/routes.php');

        view()->addLocation(base_path('vendor/ifp/sale-advert-search/resources/views'));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InputParser::class, function ($app) {

            $mapping = [
                'pmn' => 'minimum_price',
                'pmx' => 'maximum_price',
                'bedrooms' => 'minimum_bedrooms',
            ];

            $input_parser = new InputParser(request()->all(), $mapping);

            return $input_parser;
        });
    }
}
