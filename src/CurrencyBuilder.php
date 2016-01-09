<?php

namespace IFP\Adverts;

class CurrencyBuilder
{
    private $data_feed_downloader;
    private $rates;
    private $rates_with_currency_code_keys;
    private $required_currency_codes;

    public function __construct(DataFeedDownloader $data_feed_downloader)
    {
        $this->data_feed_downloader = $data_feed_downloader;
        $this->rates = json_decode($this->data_feed_downloader->data(), true);
        $this->mapRateKeys();
    }

    public function getRates()
    {
        return $this->rates;
    }

    public function mapRateKeys()
    {
        $this->rates_with_currency_code_keys = [];

        foreach($this->rates as $rate)
        {
            $this->rates_with_currency_code_keys[$rate['toCurrency']] = $rate;
        }
    }

    public function getRate($currency_code)
    {
        return $this->rates_with_currency_code_keys[$currency_code]['rate'];
    }

    public function requireRates($currency_codes)
    {
        $this->required_currency_codes = $currency_codes;
    }

    public function requiredRates()
    {
        $required_rates = array_filter($this->rates_with_currency_code_keys, function ($rate) {
            if(in_array($rate['toCurrency'], $this->required_currency_codes)) {
                return true;
            }
        });

        $required_rates = array_map(function ($rate) {
           return $rate['rate'];
        }, $required_rates);

        $required_rates['EUR'] = 1;

        return $required_rates;
    }

    public function build()
    {
        return new Currency($this->requiredRates());
    }
}
