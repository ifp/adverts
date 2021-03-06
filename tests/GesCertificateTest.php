<?php

use IFP\Adverts\GesCertificate;

class GesCertificateTest extends PHPUnit_Framework_TestCase
{
    private function assertNumber($expected, $actual)
    {
        $number_of_expected_decimal_places = strlen(substr(strrchr($expected, "."), 1));
        $number_of_actual_decimal_places = strlen(substr(strrchr($actual, "."), 1));

        $this->assertEquals(0, bccomp($expected, $actual, $number_of_expected_decimal_places));
        $this->assertEquals($number_of_expected_decimal_places, $number_of_actual_decimal_places);
    }

    public function testGesNumberIsSetWithValidGesNumber()
    {
        $subject = new GesCertificate(['number' => 600]);
        $this->assertEquals(600, $subject->number());

        $subject = new GesCertificate(['number' => 601]);
        $this->assertEquals('Unknown', $subject->number());
    }


    public function testGesLetterIsSetToAWhenTheGesNumberIsUnder6()
    {
        $subject = new GesCertificate(['number' => 1]);
        $this->assertEquals('A', $subject->letter());

        $subject = new GesCertificate(['number' => 5.9]);
        $this->assertEquals('A', $subject->letter());
    }

    public function testGesLetterIsSetToBWhenTheGesNumberIs6()
    {
        $subject = new GesCertificate(['number' => 6]);
        $this->assertEquals('B', $subject->letter());
    }

    public function testGesLetterIsSetToBWhenTheGesNumberIsBetween6And11()
    {
        $subject = new GesCertificate(['number' => 6.1]);
        $this->assertEquals('B', $subject->letter());

        $subject = new GesCertificate(['number' => 10.9]);
        $this->assertEquals('B', $subject->letter());
    }

    public function testGesLetterIsSetToCWhenTheGesNumberIs11()
    {
        $subject = new GesCertificate(['number' => 11]);
        $this->assertEquals('C', $subject->letter());
    }

    public function testGesLetterIsSetToCWhenTheGesNumberIsBetween11And21()
    {
        $subject = new GesCertificate(['number' => 11.1]);
        $this->assertEquals('C', $subject->letter());

        $subject = new GesCertificate(['number' => 20.9]);
        $this->assertEquals('C', $subject->letter());
    }

    public function testGesLetterIsSetToDWhenTheGesNumberIs21()
    {
        $subject = new GesCertificate(['number' => 21]);
        $this->assertEquals('D', $subject->letter());
    }

    public function testGesLetterIsSetToDWhenTheGesNumberIsBetween21And36()
    {
        $subject = new GesCertificate(['number' => 21.1]);
        $this->assertEquals('D', $subject->letter());

        $subject = new GesCertificate(['number' => 35.9]);
        $this->assertEquals('D', $subject->letter());
    }

    public function testGesLetterIsSetToEWhenTheGesNumberIs36()
    {
        $subject = new GesCertificate(['number' => 36]);
        $this->assertEquals('E', $subject->letter());
    }

    public function testGesLetterIsSetToEWhenTheGesNumberIsBetween36And56()
    {
        $subject = new GesCertificate(['number' => 36.1]);
        $this->assertEquals('E', $subject->letter());

        $subject = new GesCertificate(['number' => 55.9]);
        $this->assertEquals('E', $subject->letter());
    }

    public function testGesLetterIsSetToFWhenTheGesNumberIs56()
    {
        $subject = new GesCertificate(['number' => 56]);
        $this->assertEquals('F', $subject->letter());
    }

    public function testGesLetterIsSetToFWhenTheGesNumberIsBetween56And81()
    {
        $subject = new GesCertificate(['number' => 56.1]);
        $this->assertEquals('F', $subject->letter());

        $subject = new GesCertificate(['number' => 80.9]);
        $this->assertEquals('F', $subject->letter());
    }

    public function testGesLetterIsSetToGWhenTheGesNumberIs81AndOver()
    {
        $subject = new GesCertificate(['number' => 81]);
        $this->assertEquals('G', $subject->letter());

        $subject = new GesCertificate(['number' => 120]);
        $this->assertEquals('G', $subject->letter());
    }

    public function testGesLetterIsSetToAWhenTheGesLetterGivenIsNotAButTheNumberIsInTheARange()
    {
        $subject = new GesCertificate(['letter' => 'G', 'number' => 3]);
        $this->assertEquals('A', $subject->letter());
        $this->assertEquals(3, $subject->number());
    }

    public function testGesLetterIsSetToBWhenTheGesLetterGivenIsNotBButTheNumberIsInTheBRange()
    {
        $subject = new GesCertificate(['letter' => 'G', 'number' => 7]);
        $this->assertEquals('B', $subject->letter());
        $this->assertEquals(7, $subject->number());
    }

    public function testGesLetterIsSetToCWhenTheGesLetterGivenIsNotCButTheNumberIsInTheCRange()
    {
        $subject = new GesCertificate(['letter' => 'G', 'number' => 14]);
        $this->assertEquals('C', $subject->letter());
        $this->assertEquals(14, $subject->number());
    }

    public function testGesLetterIsSetToDWhenTheGesLetterGivenIsNotDButTheNumberIsInTheDRange()
    {
        $subject = new GesCertificate(['letter' => 'G', 'number' => 27]);
        $this->assertEquals('D', $subject->letter());
        $this->assertEquals(27, $subject->number());
    }

    public function testGesLetterIsSetToEWhenTheGesLetterGivenIsNotEButTheNumberIsInTheERange()
    {
        $subject = new GesCertificate(['letter' => 'G', 'number' => 40]);
        $this->assertEquals('E', $subject->letter());
        $this->assertEquals(40, $subject->number());
    }

    public function testGesLetterIsSetToFWhenTheGesLetterGivenIsNotFButTheNumberIsInTheFRange()
    {
        $subject = new GesCertificate(['letter' => 'G', 'number' => 72]);
        $this->assertEquals('F', $subject->letter());
        $this->assertEquals(72, $subject->number());
    }

    public function testGesLetterIsSetToGWhenTheGesLetterGivenIsNotGButTheNumberIsInTheGRange()
    {
        $subject = new GesCertificate(['letter' => 'A', 'number' => 82]);
        $this->assertEquals('G', $subject->letter());
        $this->assertEquals(82, $subject->number());
    }

    public function testTheGesCertificateIsSetCorrectlyWithAtLeastOneValidValue()
    {
        $subject = new GesCertificate(['letter' => '', 'number' => '']);
        $this->assertEquals(false, $subject->isValid());

        $subject = new GesCertificate(['letter' => 'C', 'number' => '']);
        $this->assertEquals(true, $subject->isValid());

        $subject = new GesCertificate(['letter' => '', 'number' => '51']);
        $this->assertEquals(true, $subject->isValid());

        $subject = new GesCertificate(['letter' => 'G', 'number' => '51']);
        $this->assertEquals(true, $subject->isValid());

        $subject = new GesCertificate(['letter' => 'A', 'number' => '41']);
        $this->assertEquals(true, $subject->isValid());
    }
}
