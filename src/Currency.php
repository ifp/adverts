<?php

namespace IFP\Adverts;

class Currency
{
    private $code;
    private $currencies;
    private $rates;

    public function __construct($rates)
    {
        $this->rates = $rates;

        $this->code = 'EUR';

        $this->currencies = [
            'EUR' => '€',
            'GBP' => '£',
            'USD' => '$',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'Fr.',
            'ZAR' => 'R',
        ];
    }

    public function setCurrency($code)
    {
        if (array_key_exists($code, $this->currencies)) {
            $this->code = $code;
        }
    }

    public function code()
    {
        return $this->code;
    }

    public function symbol()
    {
        if(isset($this->currencies[$this->code()])) {
            return $this->currencies[$this->code()];
        }

        return '€';
    }

    public function isCurrency($currency)
    {
        return $this->code() == $currency;
    }

    public function isNotCurrency($currency)
    {
        return !$this->isCurrency($currency);
    }

    public function convertFromEuros($amount = 0, $code = null)
    {
        if ($code === null) {
            $code = $this->code();
        }

        return (int)floor($amount * $this->rates[$code]);
    }

    public function convertToEuros($amount = 0, $code = null)
    {
        if ($code === null) {
            $code = $this->code();
        }

        return (int)round($amount / $this->rates[$code]);
    }

    public function format($value, $currencies = [])
    {
        $currencies = $this->removeEuros($currencies);

        $formatted_currencies = $this->formatValueInCurrency($value, 'EUR');

        $converted_currencies = $this->convertCurrencies($value, $currencies);

        $converted_currencies = array_map(function ($value) {
            return ' (~' . $value . ')';
        }, $converted_currencies);

        $formatted_currencies .= implode('', $converted_currencies);

        return $formatted_currencies;
    }

    public function convertCurrencies($value, $currencies)
    {
        if((count($currencies) == 0) && ($this->code() != 'EUR')) {
            $currencies = [$this->code()];
        }

        $converted_currencies = [];

        foreach ($currencies as $currency_code) {
            $converted_currencies[$currency_code] = $this->formatValueInCurrency($value, $currency_code);
        }

        return $converted_currencies;
    }

    private function removeEuros($currencies)
    {
        if (@$currencies[0] == 'EUR') {
            array_shift($currencies);
        }

        return $currencies;
    }

    private function formatValue($value)
    {
        return number_format($value, 0, '.', ',');
    }

    private function formatValueInCurrency($value_in_euros, $currency)
    {
        return $this->currencies[$currency] .  $this->formatValue($this->convertFromEuros($value_in_euros, $currency));
    }
}
