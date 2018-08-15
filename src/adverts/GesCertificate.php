<?php

namespace IFP\Adverts;

class GesCertificate extends EnergyCertificate
{
    public function __construct($values)
    {
        $this->setLetterThresholds();
        $this->setLetter($values);
        $this->setNumber($values);
    }

    protected function setLetterThresholds()
    {
        $this->letter_thresholds['start']['A'] = 0;
        $this->letter_thresholds['end']['A'] = 6;

        $this->letter_thresholds['start']['B'] = 6;
        $this->letter_thresholds['end']['B'] = 11;

        $this->letter_thresholds['start']['C'] = 11;
        $this->letter_thresholds['end']['C'] = 21;

        $this->letter_thresholds['start']['D'] = 21;
        $this->letter_thresholds['end']['D'] = 36;

        $this->letter_thresholds['start']['E'] = 36;
        $this->letter_thresholds['end']['E'] = 56;

        $this->letter_thresholds['start']['F'] = 56;
        $this->letter_thresholds['end']['F'] = 81;

        $this->letter_thresholds['start']['G'] = 81;
        $this->letter_thresholds['end']['G'] = 600;
    }
}
