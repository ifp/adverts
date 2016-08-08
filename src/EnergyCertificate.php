<?php

namespace IFP\Adverts;

abstract class EnergyCertificate
{
    protected $number = false;
    protected $letter = false;
    protected $letter_thresholds;

    public function __construct($values)
    {
        $this->setLetterThresholds();
        $this->setLetter($values);
        $this->setNumber($values);
    }

    abstract protected function setLetterThresholds();

    protected function setLetter($values)
    {
        if (isset($values['letter']) && in_array($values['letter'], ['A', 'B', 'C', 'D', 'E', 'F', 'G'], true)) {
            $this->letter = $values['letter'];
        } else {
            $this->letter = '';
        }
    }

    protected function setNumber($values)
    {
        if ($this->isNumberValid($values)) {
            $this->number = $this->roundDown($values['number'], 1);

            if ($this->number < $this->letterThresholdEnd('A')) {
                $this->letter = 'A';
            } elseif ($this->number >= $this->letterThresholdStart('B') && $this->number < $this->letterThresholdEnd('B')) {
                $this->letter = 'B';
            } elseif ($this->number >= $this->letterThresholdStart('C') && $this->number < $this->letterThresholdEnd('C')) {
                $this->letter = 'C';
            } elseif ($this->number >= $this->letterThresholdStart('D') && $this->number < $this->letterThresholdEnd('D')) {
                $this->letter = 'D';
            } elseif ($this->number >= $this->letterThresholdStart('E') && $this->number < $this->letterThresholdEnd('E')) {
                $this->letter = 'E';
            } elseif ($this->number >= $this->letterThresholdStart('F') && $this->number < $this->letterThresholdEnd('F')) {
                $this->letter = 'F';
            } else {
                $this->letter = 'G';
            }
        } else {
            $this->number = '';
        }
    }

    private function isNumberValid($values)
    {
        return isset($values['number'])
        && is_numeric($values['number'])
        && ($values['number'] > 0)
        && ($values['number'] <= $this->letter_thresholds['end']['G']);
    }

    private function letterThresholdStart($letter)
    {
        return $this->letter_thresholds['start'][$letter];
    }

    private function letterThresholdEnd($letter)
    {
        return $this->letter_thresholds['end'][$letter];
    }

    public function isValid()
    {
        return $this->numberIsSet() || $this->letterIsSet();
    }

    private function numberIsSet()
    {
        return ($this->number() != false) && ($this->number() != '?');
    }

    private function letterIsSet()
    {
        return ($this->letter() != false) && ($this->letter() != '?');
    }

    public function number()
    {
        return $this->number;
    }

    public function letter()
    {
        return $this->letter;
    }

    private function roundDown($amount, $precision)
    {
        $fig = (int)str_pad('1', $precision + 1, '0');

        return (floor($amount * $fig) / $fig);
    }
}
