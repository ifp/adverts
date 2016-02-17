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

        $currencies = $this->addCurrencyThatIsSet($currencies);

        $converted_currencies = $this->convertCurrencies($value);

        $filtered_converted_currencies = $this->filterCurrencies($converted_currencies, $currencies);

        $formatted_currencies = $this->formatValueInCurrency($value, 'EUR');

        $formatted_filtered_converted_currencies = $this->formatFilteredCurrencies($filtered_converted_currencies);

        $formatted_currencies .= implode('', $formatted_filtered_converted_currencies);

        return $formatted_currencies;
    }

    private function formatFilteredCurrencies($filtered_converted_currencies)
    {
        return array_map(function ($value) {
            return ' (~' . $value . ')';
        }, $filtered_converted_currencies);
    }

    private function filterCurrencies($converted_currencies, $currencies)
    {
        return array_filter($converted_currencies, function ($currency_code) use ($currencies) {
            return in_array($currency_code, $currencies);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function addCurrencyThatIsSet($currencies)
    {
        if((count($currencies) == 0) && ($this->code() != 'EUR')) {
            $currencies = [$this->code()];
        }

        return $currencies;
    }

    public function convertCurrencies($value)
    {
        $currencies = $this->currencies;

        unset($currencies['EUR']);

        $converted_currencies = [];

        foreach ($currencies as $currency_code => $currency_symbol) {
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

    public function formatValueInCurrency($value_in_euros, $currency)
    {
        return $this->currencies[$currency] .  $this->formatValue($this->convertFromEuros($value_in_euros, $currency));
    }
}
