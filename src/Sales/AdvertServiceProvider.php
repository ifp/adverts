<?php

namespace IFP\Adverts;

use Illuminate\Support\ServiceProvider;

class AdvertServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require base_path('vendor/ifp/advert-search/src/routes.php');

        view()->addLocation(base_path('vendor/ifp/adverts/resources/views'));
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
