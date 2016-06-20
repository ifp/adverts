<?php

use IFP\Adverts\CurrencyBuilder;
use IFP\Adverts\DataFeedDownloader;

class CurrencyBuilderTest extends PHPUnit_Framework_TestCase
{
    private $data_feed_downloader;

    public function setUp()
    {
        parent::setUp();
        $this->data_feed_downloader = Mockery::spy(DataFeedDownloader::class);
        $this->data_feed_downloader->shouldReceive('data')->andReturn('[{"fromCurrency":"EUR","toCurrency":"AED","rate":4.0102,"name":"Arab Emirate Dirham"},{"fromCurrency":"EUR","toCurrency":"AUD","rate":1.5639,"name":"Australian Dollar"},{"fromCurrency":"EUR","toCurrency":"BBD","rate":2.1824,"name":"Barbadian Dollar"},{"fromCurrency":"EUR","toCurrency":"BHD","rate":0.4114,"name":"Bahraini Dinar"},{"fromCurrency":"EUR","toCurrency":"BSD","rate":1.0937,"name":"Bahamian Dollar"},{"fromCurrency":"EUR","toCurrency":"BGN","rate":1.0000,"name":"Bulgarian Lev"},{"fromCurrency":"EUR","toCurrency":"CAD","rate":1.5426,"name":"Canadian Dollar"},{"fromCurrency":"EUR","toCurrency":"CHF","rate":1.0863,"name":"Swiss Franc"},{"fromCurrency":"EUR","toCurrency":"CNY","rate":7.1993,"name":"Chinese Yuan Renminbi*"},{"fromCurrency":"EUR","toCurrency":"CZK","rate":27.0740,"name":"Czech Koruny"},{"fromCurrency":"EUR","toCurrency":"DKK","rate":7.4737,"name":"Danish Krone"},{"fromCurrency":"EUR","toCurrency":"GBP","rate":0.7521,"name":"British Pound Sterling"},{"fromCurrency":"EUR","toCurrency":"GHS","rate":4.2912,"name":"Ghanaian Cedi"},{"fromCurrency":"EUR","toCurrency":"HKD","rate":8.4801,"name":"Hong Kong Dollar"},{"fromCurrency":"EUR","toCurrency":"HRK","rate":7.6700,"name":"Croatian Kune"},{"fromCurrency":"EUR","toCurrency":"HUF","rate":316.9100,"name":"Hungarian Forint"},{"fromCurrency":"EUR","toCurrency":"ILS","rate":4.2961,"name":"Israeli Shekel"},{"fromCurrency":"EUR","toCurrency":"JPY","rate":128.1400,"name":"Japanese Yen"},{"fromCurrency":"EUR","toCurrency":"KYD","rate":0.8940,"name":"Cayman Island Dollar"},{"fromCurrency":"EUR","toCurrency":"MAD","rate":10.7980,"name":"Moroccan Dirham"},{"fromCurrency":"EUR","toCurrency":"MXN","rate":19.5840,"name":"Mexican Peso"},{"fromCurrency":"EUR","toCurrency":"NOK","rate":9.6924,"name":"Norwegian Krone"},{"fromCurrency":"EUR","toCurrency":"NZD","rate":1.6685,"name":"New Zealand Dollar"},{"fromCurrency":"EUR","toCurrency":"PLN","rate":4.3707,"name":"Polish Zloty"},{"fromCurrency":"EUR","toCurrency":"RUB","rate":81.6260,"name":"Russian Rouble"},{"fromCurrency":"EUR","toCurrency":"SEK","rate":9.2862,"name":"Swedish Krona"},{"fromCurrency":"EUR","toCurrency":"SGD","rate":1.5717,"name":"Singapore Dollar"},{"fromCurrency":"EUR","toCurrency":"THB","rate":39.6030,"name":"Thai Baht"},{"fromCurrency":"EUR","toCurrency":"TRY","rate":3.2876,"name":"Turkish Lira"},{"fromCurrency":"EUR","toCurrency":"USD","rate":1.0919,"name":"US Dollar"},{"fromCurrency":"EUR","toCurrency":"XCD","rate":2.9467,"name":"East Carribean Dollar"},{"fromCurrency":"EUR","toCurrency":"ZAR","rate":17.7990,"name":"South African Rand"}]');
    }

    public function testAllRatesAreReturned()
    {
        $currency_builder = new CurrencyBuilder($this->data_feed_downloader);

        $this->assertEquals(json_decode('[{"fromCurrency":"EUR","toCurrency":"AED","rate":4.0102,"name":"Arab Emirate Dirham"},{"fromCurrency":"EUR","toCurrency":"AUD","rate":1.5639,"name":"Australian Dollar"},{"fromCurrency":"EUR","toCurrency":"BBD","rate":2.1824,"name":"Barbadian Dollar"},{"fromCurrency":"EUR","toCurrency":"BHD","rate":0.4114,"name":"Bahraini Dinar"},{"fromCurrency":"EUR","toCurrency":"BSD","rate":1.0937,"name":"Bahamian Dollar"},{"fromCurrency":"EUR","toCurrency":"BGN","rate":1.0000,"name":"Bulgarian Lev"},{"fromCurrency":"EUR","toCurrency":"CAD","rate":1.5426,"name":"Canadian Dollar"},{"fromCurrency":"EUR","toCurrency":"CHF","rate":1.0863,"name":"Swiss Franc"},{"fromCurrency":"EUR","toCurrency":"CNY","rate":7.1993,"name":"Chinese Yuan Renminbi*"},{"fromCurrency":"EUR","toCurrency":"CZK","rate":27.0740,"name":"Czech Koruny"},{"fromCurrency":"EUR","toCurrency":"DKK","rate":7.4737,"name":"Danish Krone"},{"fromCurrency":"EUR","toCurrency":"GBP","rate":0.7521,"name":"British Pound Sterling"},{"fromCurrency":"EUR","toCurrency":"GHS","rate":4.2912,"name":"Ghanaian Cedi"},{"fromCurrency":"EUR","toCurrency":"HKD","rate":8.4801,"name":"Hong Kong Dollar"},{"fromCurrency":"EUR","toCurrency":"HRK","rate":7.6700,"name":"Croatian Kune"},{"fromCurrency":"EUR","toCurrency":"HUF","rate":316.9100,"name":"Hungarian Forint"},{"fromCurrency":"EUR","toCurrency":"ILS","rate":4.2961,"name":"Israeli Shekel"},{"fromCurrency":"EUR","toCurrency":"JPY","rate":128.1400,"name":"Japanese Yen"},{"fromCurrency":"EUR","toCurrency":"KYD","rate":0.8940,"name":"Cayman Island Dollar"},{"fromCurrency":"EUR","toCurrency":"MAD","rate":10.7980,"name":"Moroccan Dirham"},{"fromCurrency":"EUR","toCurrency":"MXN","rate":19.5840,"name":"Mexican Peso"},{"fromCurrency":"EUR","toCurrency":"NOK","rate":9.6924,"name":"Norwegian Krone"},{"fromCurrency":"EUR","toCurrency":"NZD","rate":1.6685,"name":"New Zealand Dollar"},{"fromCurrency":"EUR","toCurrency":"PLN","rate":4.3707,"name":"Polish Zloty"},{"fromCurrency":"EUR","toCurrency":"RUB","rate":81.6260,"name":"Russian Rouble"},{"fromCurrency":"EUR","toCurrency":"SEK","rate":9.2862,"name":"Swedish Krona"},{"fromCurrency":"EUR","toCurrency":"SGD","rate":1.5717,"name":"Singapore Dollar"},{"fromCurrency":"EUR","toCurrency":"THB","rate":39.6030,"name":"Thai Baht"},{"fromCurrency":"EUR","toCurrency":"TRY","rate":3.2876,"name":"Turkish Lira"},{"fromCurrency":"EUR","toCurrency":"USD","rate":1.0919,"name":"US Dollar"},{"fromCurrency":"EUR","toCurrency":"XCD","rate":2.9467,"name":"East Carribean Dollar"},{"fromCurrency":"EUR","toCurrency":"ZAR","rate":17.7990,"name":"South African Rand"}]', true),
            $currency_builder->getRates());
    }

    public function testGbpRateIsReturned()
    {
        $currency_builder = new CurrencyBuilder($this->data_feed_downloader);

        $this->assertEquals(0.7521, $currency_builder->getRate('GBP'));
    }

    public function testRequiredRatesAreReturnedInFormatRequiredToBuildCurrencyObject()
    {
        $currency_builder = new CurrencyBuilder($this->data_feed_downloader);

        $currency_builder->requireRates(['AUD', 'CAD', 'CHF', 'GBP', 'USD', 'ZAR', 'EUR']);

        $expected_currencies = [
            'AUD' => 1.5639,
            'CAD' => 1.5426,
            'CHF' => 1.0863,
            'GBP' => 0.7521,
            'USD' => 1.0919,
            'ZAR' => 17.799,
            'EUR' => 1,
        ];

        $this->assertEquals($expected_currencies, $currency_builder->requiredRates(), '', 0.0001);
    }
}
