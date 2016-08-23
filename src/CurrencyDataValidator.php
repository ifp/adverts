<?php
namespace IFP\Adverts;


class CurrencyDataValidator
{
    private $required_currencies;

    public function __construct($required_currencies)
    {
        $this->required_currencies = $required_currencies;
    }

    public function validate($data)
    {
        $data_array = json_decode($data, true);

        if($data_array == null) {
            return false;
        }

        $currencies_in_data = array_map(function($item) {
            return $item['toCurrency'];
        }, $data_array);

        foreach($this->required_currencies as $required_currency) {
            if(in_array($required_currency, $currencies_in_data) === false) {
                return false;
            }
        }

        return true;
    }
}