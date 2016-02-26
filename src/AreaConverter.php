<?php

namespace IFP\Adverts;

class AreaConverter
{
    private $base_amount = 0;
    private $conversion_unit = 'm²';
    private $base_unit = 'm²';

    public function selectConversionUnit($unit)
    {
        if (in_array($unit, ['m²', 'Acres', 'Hectares'])) {
            $this->conversion_unit = $unit;
        }
    }

    public function conversionUnit()
    {
        return $this->conversion_unit;
    }

    public function conversionUnitAbbreviation()
    {
        if ($this->conversionUnit() == 'Acres') {
            return 'ac';
        } elseif ($this->conversionUnit() == 'Hectares') {
            return 'ha';
        }

        return $this->conversion_unit;
    }

    public function isUnit($unit)
    {
        return $this->conversion_unit == $unit;
    }

    private function baseUnit()
    {
        return $this->base_unit;
    }

    public function from($base_amount, $base_unit)
    {
        $this->base_amount = $base_amount;

        if(in_array($base_unit, ['m²', 'Acres', 'Hectares'])) {
            $this->base_unit = $base_unit;
        }

        return $this;
    }

    public function fromSquareMetres($base_amount)
    {
        $this->base_amount = $base_amount;
        $this->base_unit = 'm²';

        return $this;
    }

    public function to($unit)
    {
        $method_name = 'to' . $unit;

        return $this->{$method_name}();
    }

    public function toSelectedConversionUnit()
    {
        $method_name = 'to' . $this->conversionUnit();

        return $this->{$method_name}();
    }

    public function tom²()
    {
        return $this->toSquareMetres();
    }

    public function toSquareMetres()
    {
        if (!is_numeric($this->base_amount)) {
            return 'unknown';
        }

        if ($this->baseUnit() == 'Acres') {
            return (int) floor($this->base_amount * 4046.86);
        } elseif ($this->baseUnit() == 'Hectares') {
            return (int) floor($this->base_amount * 10000);
        }

        return (int) floor($this->base_amount);
    }

    public function toAcres()
    {
        if (!is_numeric($this->base_amount)) {
            return 'unknown';
        }

        if ($this->baseUnit() == 'm²') {
            return $this->roundDown($this->base_amount / 4046.86, 2);
        } elseif ($this->baseUnit() == 'Hectares') {
            return $this->roundDown($this->base_amount * 2.47105, 2);
        }

        return $this->roundDown($this->base_amount, 2);
    }

    public function toHectares()
    {
        if (!is_numeric($this->base_amount)) {
            return 'unknown';
        }

        if ($this->baseUnit() == 'm²') {
            return $this->roundDown($this->base_amount / 10000, 2);
        } elseif ($this->baseUnit() == 'Acres') {
            return $this->roundDown($this->base_amount / 2.47105, 2);
        }

        return $this->roundDown($this->base_amount, 2);
    }

    private function roundDown($amount, $precision)
    {
        $fig = (int) str_pad('1', $precision + 1, '0');

        return (floor($amount * $fig) / $fig);
    }
}
