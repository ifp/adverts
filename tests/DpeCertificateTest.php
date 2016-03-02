<?php

use IFP\Adverts\DpeCertificate;

class DpeCertificateTest extends PHPUnit_Framework_TestCase
{
    protected function assertNumber($expected, $actual)
    {
        $number_of_expected_decimal_places = strlen(substr(strrchr($expected, "."), 1));
        $number_of_actual_decimal_places = strlen(substr(strrchr($actual, "."), 1));

        $this->assertEquals(0, bccomp($expected, $actual, $number_of_expected_decimal_places));
        $this->assertEquals($number_of_expected_decimal_places, $number_of_actual_decimal_places);
    }

    public function testDpeNumberIsSetWithValidDpeNumber()
    {
        $subject = new DpeCertificate(['number' => 1000]);
        $this->assertEquals(1000, $subject->number());

        $subject = new DpeCertificate(['number' => 1001]);
        $this->assertEquals('?', $subject->number());
    }

    public function testDpeLetterIsSetToAWhenTheDpeNumberIsUnder51()
    {
        $subject = new DpeCertificate(['number' => 1]);
        $this->assertEquals('A', $subject->letter());

        $subject = new DpeCertificate(['number' => 50.99]);
        $this->assertEquals('A', $subject->letter());
    }

    public function testDpeLetterIsSetToBWhenTheDpeNumberIs51()
    {
        $subject = new DpeCertificate(['number' => 51]);
        $this->assertEquals('B', $subject->letter());
    }

    public function testDpeLetterIsSetToBWhenTheDpeNumberIsBetween51And91()
    {
        $subject = new DpeCertificate(['number' => 51.01]);
        $this->assertEquals('B', $subject->letter());

        $subject = new DpeCertificate(['number' => 90.99]);
        $this->assertEquals('B', $subject->letter());
    }

    public function testDpeLetterIsSetToCWhenTheDpeNumberIs91()
    {
        $subject = new DpeCertificate(['number' => 91]);
        $this->assertEquals('C', $subject->letter());
    }

    public function testDpeLetterIsSetToCWhenTheDpeNumberIsBetween91And151()
    {
        $subject = new DpeCertificate(['number' => 91.01]);
        $this->assertEquals('C', $subject->letter());

        $subject = new DpeCertificate(['number' => 150.99]);
        $this->assertEquals('C', $subject->letter());
    }

    public function testDpeLetterIsSetToDWhenTheDpeNumberIs151()
    {
        $subject = new DpeCertificate(['number' => 151]);
        $this->assertEquals('D', $subject->letter());
    }

    public function testDpeLetterIsSetToDWhenTheDpeNumberIsBetween151And231()
    {
        $subject = new DpeCertificate(['number' => 151.01]);
        $this->assertEquals('D', $subject->letter());

        $subject = new DpeCertificate(['number' => 230.99]);
        $this->assertEquals('D', $subject->letter());
    }

    public function testDpeLetterIsSetToEWhenTheDpeNumberIs231()
    {
        $subject = new DpeCertificate(['number' => 231]);
        $this->assertEquals('E', $subject->letter());
    }

    public function testDpeLetterIsSetToEWhenTheDpeNumberIsBetween231And331()
    {
        $subject = new DpeCertificate(['number' => 231.01]);
        $this->assertEquals('E', $subject->letter());

        $subject = new DpeCertificate(['number' => 330.99]);
        $this->assertEquals('E', $subject->letter());
    }

    public function testDpeLetterIsSetToFWhenTheDpeNumberIs331()
    {
        $subject = new DpeCertificate(['number' => 331]);
        $this->assertEquals('F', $subject->letter());
    }

    public function testDpeLetterIsSetToFWhenTheDpeNumberIsBetween331And451()
    {
        $subject = new DpeCertificate(['number' => 331.01]);
        $this->assertEquals('F', $subject->letter());

        $subject = new DpeCertificate(['number' => 450.99]);
        $this->assertEquals('F', $subject->letter());
    }

    public function testDpeLetterIsSetToGWhenTheDpeNumberIs451AndOver()
    {
        $subject = new DpeCertificate(['number' => 451]);
        $this->assertEquals('G', $subject->letter());

        $subject = new DpeCertificate(['number' => 651]);
        $this->assertEquals('G', $subject->letter());
    }

    public function testDpeLetterIsSetToAWhenTheDpeLetterGivenIsNotAButTheNumberIsInTheARange()
    {
        $subject = new DpeCertificate(['letter' => 'G', 'number' => 50]);
        $this->assertEquals('A', $subject->letter());
        $this->assertEquals(50, $subject->number());
    }

    public function testDpeLetterIsSetToBWhenTheDpeLetterGivenIsNotBButTheNumberIsInTheBRange()
    {
        $subject = new DpeCertificate(['letter' => 'G', 'number' => 90]);
        $this->assertEquals('B', $subject->letter());
        $this->assertEquals(90, $subject->number());
    }

    public function testDpeLetterIsSetToCWhenTheDpeLetterGivenIsNotCButTheNumberIsInTheCRange()
    {
        $subject = new DpeCertificate(['letter' => 'G', 'number' => 150]);
        $this->assertEquals('C', $subject->letter());
        $this->assertEquals(150, $subject->number());
    }

    public function testDpeLetterIsSetToDWhenTheDpeLetterGivenIsNotDButTheNumberIsInTheDRange()
    {
        $subject = new DpeCertificate(['letter' => 'G', 'number' => 230]);
        $this->assertEquals('D', $subject->letter());
        $this->assertEquals(230, $subject->number());
    }

    public function testDpeLetterIsSetToEWhenTheDpeLetterGivenIsNotEButTheNumberIsInTheERange()
    {
        $subject = new DpeCertificate(['letter' => 'G', 'number' => 330]);
        $this->assertEquals('E', $subject->letter());
        $this->assertEquals(330, $subject->number());
    }

    public function testDpeLetterIsSetToFWhenTheDpeLetterGivenIsNotFButTheNumberIsInTheFRange()
    {
        $subject = new DpeCertificate(['letter' => 'G', 'number' => 450]);
        $this->assertEquals('F', $subject->letter());
        $this->assertEquals(450, $subject->number());
    }

    public function testDpeLetterIsSetToGWhenTheDpeLetterGivenIsNotGButTheNumberIsInTheGRange()
    {
        $subject = new DpeCertificate(['letter' => 'A', 'number' => 999]);
        $this->assertEquals('G', $subject->letter());
        $this->assertEquals(999, $subject->number());
    }

    public function testTheDpeCertificateIsSetCorrectlyWithAtLeastOneValidValue()
    {
        $subject = new DpeCertificate(['letter' => '', 'number' => '']);
        $this->assertEquals(false, $subject->isValid());

        $subject = new DpeCertificate(['letter' => 'C', 'number' => '']);
        $this->assertEquals(true, $subject->isValid());

        $subject = new DpeCertificate(['letter' => '', 'number' => '51']);
        $this->assertEquals(true, $subject->isValid());

        $subject = new DpeCertificate(['letter' => 'G', 'number' => '51']);
        $this->assertEquals(true, $subject->isValid());

        $subject = new DpeCertificate(['letter' => 'A', 'number' => '41']);
        $this->assertEquals(true, $subject->isValid());
    }
}
