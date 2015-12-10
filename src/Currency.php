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

    public function convertFromEuros($amount = 0)
    {
        return (int)floor($amount * $this->rates[$this->code()]);
    }
}
