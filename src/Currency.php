<?php

namespace IFP\Adverts;

class Currency
{
    private $code;
    private $currencies;

    public function __construct($code)
    {
        $this->code = $code;
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
}
