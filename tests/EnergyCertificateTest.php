<?php

/*
 * This tests the shared methods provided by EnergyCertificate.
 * We use DpeCertificate as the concrete instance,
 * but could quite easily use GesCertificate instead...
 */

use IFP\Adverts\DpeCertificate;

class EnergyCertificateTest extends PHPUnit_Framework_TestCase
{
    protected function assertNumber($expected, $actual)
    {
        $number_of_expected_decimal_places = strlen(substr(strrchr($expected, "."), 1));
        $number_of_actual_decimal_places = strlen(substr(strrchr($actual, "."), 1));

        $this->assertEquals(0, bccomp($expected, $actual, $number_of_expected_decimal_places));
        $this->assertEquals($number_of_expected_decimal_places, $number_of_actual_decimal_places);
    }

    public function testEnergyNumberIsSetWithValidEnergyNumber()
    {
        $subject = new DpeCertificate(['number' => -1]);
        $this->assertEquals('?', $subject->number());

        $subject = new DpeCertificate(['number' => 0]);
        $this->assertEquals('?', $subject->number());

        $subject = new DpeCertificate(['number' => 0.1]);
        $this->assertEquals(0.1, $subject->number());

        $subject = new DpeCertificate(['number' => '0.1']);
        $this->assertEquals(0.1, $subject->number());

        $subject = new DpeCertificate(['number' => 1]);
        $this->assertEquals(1, $subject->number());

        $subject = new DpeCertificate(['number' => 60]);
        $this->assertEquals(60, $subject->number());

        $subject = new DpeCertificate(['number' => 60.7]);
        $this->assertEquals(60.7, $subject->number());

        $subject = new DpeCertificate(['number' => 60.7654321]);
        $this->assertNumber(60.76, $subject->number());

        $subject = new DpeCertificate(['number' => '60.7654321']);
        $this->assertEquals(60.76, $subject->number());

        $subject = new DpeCertificate(['number' => null]);
        $this->assertEquals('?', $subject->number());

        $subject = new DpeCertificate(['number' => '']);
        $this->assertEquals('?', $subject->number());

        $subject = new DpeCertificate(['number' => 'foo']);
        $this->assertEquals('?', $subject->number());

        $subject = new DpeCertificate(['number' => true]);
        $this->assertEquals('?', $subject->number());

        $subject = new DpeCertificate(['number' => false]);
        $this->assertEquals('?', $subject->number());

        $subject = new DpeCertificate(['number' => []]);
        $this->assertEquals('?', $subject->number());

        $subject = new DpeCertificate(['number' => new stdClass()]);
        $this->assertEquals('?', $subject->number());
    }

    public function testEnergyLetterIsSetToAWhenTheEnergyLetterIsA()
    {
        $subject = new DpeCertificate(['letter' => 'A']);
        $this->assertEquals('A', $subject->letter());
    }

    public function testEnergyLetterIsSetToBWhenTheEnergyLetterIsB()
    {
        $subject = new DpeCertificate(['letter' => 'B']);
        $this->assertEquals('B', $subject->letter());
    }

    public function testEnergyLetterIsSetToCWhenTheEnergyLetterIsC()
    {
        $subject = new DpeCertificate(['letter' => 'C']);
        $this->assertEquals('C', $subject->letter());
    }

    public function testEnergyLetterIsSetToDWhenTheEnergyLetterIsD()
    {
        $subject = new DpeCertificate(['letter' => 'D']);
        $this->assertEquals('D', $subject->letter());
    }

    public function testEnergyLetterIsSetToEWhenTheEnergyLetterIsE()
    {
        $subject = new DpeCertificate(['letter' => 'E']);
        $this->assertEquals('E', $subject->letter());
    }

    public function testEnergyLetterIsSetToFWhenTheEnergyLetterIsF()
    {
        $subject = new DpeCertificate(['letter' => 'F']);
        $this->assertEquals('F', $subject->letter());
    }

    public function testEnergyLetterIsSetToGWhenTheEnergyLetterIsG()
    {
        $subject = new DpeCertificate(['letter' => 'G']);
        $this->assertEquals('G', $subject->letter());
    }

    public function testEnergyLetterIsSetToUnknownWhenTheEnergyLetterIsInvalid()
    {
        $subject = new DpeCertificate(['letter' => null]);
        $this->assertEquals('?', $subject->letter());

        $subject = new DpeCertificate(['letter' => false]);
        $this->assertEquals('?', $subject->letter());

        $subject = new DpeCertificate(['letter' => '']);
        $this->assertEquals('?', $subject->letter());

        $subject = new DpeCertificate(['letter' => 'foo']);
        $this->assertEquals('?', $subject->letter());

        $subject = new DpeCertificate(['letter' => []]);
        $this->assertEquals('?', $subject->letter());

        $subject = new DpeCertificate(['letter' => 10]);
        $this->assertEquals('?', $subject->letter());

        $subject = new DpeCertificate(['letter' => true]);
        $this->assertEquals('?', $subject->letter());

        $subject = new DpeCertificate(['letter' => new stdClass()]);
        $this->assertEquals('?', $subject->letter());
    }

    public function testEnergyLetterAndNumberIsSetToUnknownWhenNeitherTheEnergyLetterOrNumberIsGiven()
    {
        $subject = new DpeCertificate(['letter' => '', 'number' => '']);
        $this->assertEquals('?', $subject->letter());
        $this->assertEquals('?', $subject->number());
    }

}
