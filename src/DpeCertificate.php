<?php

namespace IFP\Adverts;

class DpeCertificate extends EnergyCertificate
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
        $this->letter_thresholds['end']['A'] = 51;

        $this->letter_thresholds['start']['B'] = 51;
        $this->letter_thresholds['end']['B'] = 91;

        $this->letter_thresholds['start']['C'] = 91;
        $this->letter_thresholds['end']['C'] = 151;

        $this->letter_thresholds['start']['D'] = 151;
        $this->letter_thresholds['end']['D'] = 231;

        $this->letter_thresholds['start']['E'] = 231;
        $this->letter_thresholds['end']['E'] = 331;

        $this->letter_thresholds['start']['F'] = 331;
        $this->letter_thresholds['end']['F'] = 451;

        $this->letter_thresholds['start']['G'] = 451;
        $this->letter_thresholds['end']['G'] = 1000;
    }
}
