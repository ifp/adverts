<?php

namespace IFP\Adverts;

class Currency
{
    private $code;
    private $currencies;
    private $rates;

    public function __construct($rates)
    {
        //dd($rates);
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

        return (int)floor((int)$amount * $this->rates[$code]);
    }

    public function convertToEuros($amount = 0, $code = null)
    {
        if ($code === null) {
            $code = $this->code();
        }
        
        return (int)round($amount / $this->rates[$code]);
    }

    public function convertCurrenciesFromEuros($value)
    {
        foreach ($this->currencies as $currency_code => $currency_symbol) {
            $converted_currencies[$currency_code] = $this->convertFromEuros($value, $currency_code);
        }

        return $converted_currencies;
    }

    public function formatCurrenciesFromEuros($value)
    {
        foreach ($this->currencies as $currency_code => $currency_symbol) {
            $converted_currencies[$currency_code] = $this->formatValueInCurrency($value, $currency_code);
        }

        return $converted_currencies;
    }

    public function formatValueInCurrency($value_in_euros, $currency)
    {
        return $this->currencies[$currency] .  $this->formatValue($this->convertFromEuros($value_in_euros, $currency));
    }

    public function formatValue($value)
    {
        return number_format($value, 0, '.', ',');
    }

}
