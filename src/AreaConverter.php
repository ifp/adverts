<?php

namespace IFP\Adverts;

class AreaConverter
{
    private $base_value = null;
    private $converted_value = null;
    private $conversion_unit_symbol = 'm²';
    private $base_unit_symbol = 'm²';

    private $unit_symbols = ['m²', 'ha', 'ac'];

    public function selectConversionUnit($unit)
    {
        if (in_array($unit, $this->unit_symbols)) {
            $this->conversion_unit_symbol = $unit;
        }
    }

    public function baseUnitName()
    {
        return $this->unitName($this->base_value, $this->base_unit_symbol);
    }

    public function conversionUnitName()
    {
        return $this->unitName($this->toSelectedConversionUnit()->value(), $this->conversion_unit_symbol);
    }

    public function conversionUnitSymbol()
    {
        return $this->conversion_unit_symbol;
    }

    public function isUnit($unit)
    {
        return $this->conversion_unit_symbol == $unit;
    }

    public function from($base_amount, $base_unit)
    {
        $this->base_value = $base_amount;

        if(in_array($base_unit, $this->unit_symbols)) {
            $this->base_unit_symbol = $base_unit;
        }

        return $this;
    }

    public function fromSquareMetres($base_value)
    {
        $this->base_value = $base_value;
        $this->base_unit_symbol = 'm²';

        return $this;
    }

    public function to($unit)
    {
        //Old code commented - This approach doesn't work well with lower cased symbols e.g. ac -> toac() and ha -> toha() - breaks camelCase
        //$method_name = 'to' . $unit;
        //return $this->{$method_name}();

        if($unit == 'm²') {
            return $this->toSquareMetres();
        } else if($unit == 'ha') {
            return $this->toHectares();
        } else if($unit == 'ac') {
            return $this->toAcres();
        }

        $base_amount = 'unknown';
        $base_unit = 'unknown';

        return $this;
    }

    public function toSelectedConversionUnit()
    {
        //See comment above
//        $method_name = 'to' . $this->conversion_unit_symbol;
//        return $this->{$method_name}();

        return $this->to($this->conversion_unit_symbol);
    }

    public function tom²()
    {
        return $this->toSquareMetres();
    }
    public function toSquareMetres()
    {
        $this->conversion_unit_symbol = 'm²';

        if (!is_numeric($this->base_value)) {
            $this->converted_value = 'unknown';
            return $this;
        }

        if ($this->base_unit_symbol == 'ac') {
            $this->converted_value = (int) floor($this->base_value * 4046.86);
            return $this;
        } elseif ($this->base_unit_symbol == 'ha') {
            $this->converted_value = (int) floor($this->base_value * 10000);
            return $this;
        }

        $this->converted_value = (int) floor($this->base_value);
        return $this;
    }

    public function toAcres()
    {
        $this->conversion_unit_symbol = 'ac';

        if (!is_numeric($this->base_value)) {
            $this->converted_value = 'unknown';
            return $this;
        }

        if ($this->base_unit_symbol == 'm²') {
            $this->converted_value = $this->roundDown($this->base_value / 4046.86, 2);
            return $this;
        } elseif ($this->base_unit_symbol == 'ha') {
            $this->converted_value = $this->roundDown($this->base_value * 2.47105, 2);
            return $this;
        }

        $this->converted_value = $this->roundDown($this->base_value, 2);
        return $this;
    }

    public function toHectares()
    {
        $this->conversion_unit_symbol = 'ha';

        if (!is_numeric($this->base_value)) {
            $this->converted_value = 'unknown';
            return $this;
        }

        if ($this->base_unit_symbol == 'm²') {
            $this->converted_value = $this->roundDown($this->base_value / 10000, 2);
            return $this;
        } elseif ($this->base_unit_symbol == 'ac') {
            $this->converted_value = $this->roundDown($this->base_value / 2.47105, 2);
            return $this;
        }

        $this->converted_value = $this->roundDown($this->base_value, 2);
        return $this;
    }

    public function value()
    {
        return $this->converted_value;
    }

    public function formattedValueAndUnit()
    {
        if($this->base_value === null) {
            return "(blank)";
        }

        if($this->converted_value === null) {
            return $this->base_value . ' ' . $this->baseUnitName();
        }

        return $this->converted_value . ' ' . $this->conversionUnitName();
    }

    //
    // Util functions
    //

    private function unitName($value, $unit_symbol)
    {
        if($unit_symbol == 'ac') {
            if($value == 1) {
                return 'acre';
            }
            return 'acres';
        }

        if($unit_symbol == 'ha') {
            if($value == 1) {
                return 'hectare';
            }
            return 'hectares';
        }

        // There is no reason to display 'square metres' anywhere on the site - m² is preferred
        if($unit_symbol == 'm²') {
            return 'm²';
        }

        return $unit_symbol;
    }

    private function roundDown($amount, $precision)
    {
        $fig = (int) str_pad('1', $precision + 1, '0');

        return (floor($amount * $fig) / $fig);
    }
}
