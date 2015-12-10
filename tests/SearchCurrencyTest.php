<?php

use IFP\Adverts\Currency;

class CurrencyTest extends PHPUnit_Framework_TestCase
{
    public function testItCanSetTheCurrencyToEurosAndReturnTheEuroCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency('EUR');

        $this->assertEquals('EUR', $subject->code());
        $this->assertEquals('€', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToBritishPoundsAndReturnTheBritishPoundCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency('GBP');

        $this->assertEquals('GBP', $subject->code());
        $this->assertEquals('£', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToUsDollarsAndReturnTheUsDollarCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency('USD');

        $this->assertEquals('USD', $subject->code());
        $this->assertEquals('$', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToCanadianDollarsAndReturnTheCanadianDollarCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency('CAD');

        $this->assertEquals('CAD', $subject->code());
        $this->assertEquals('C$', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToAustralianDollarsAndReturnTheAustralianDollarCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency('AUD');

        $this->assertEquals('AUD', $subject->code());
        $this->assertEquals('A$', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToSwissFrancAndReturnTheSwissFrancCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency('CHF');

        $this->assertEquals('CHF', $subject->code());
        $this->assertEquals('Fr.', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToSOuthAfricanRandAndReturnTheSouthAfricanRandCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency('ZAR');

        $this->assertEquals('ZAR', $subject->code());
        $this->assertEquals('R', $subject->symbol());
    }

    public function testItCanIdentifyWhetherACurrencyProvidedIsTheCurrencyThatHasBeenSet()
    {
        $subject = new Currency('EUR');

        $this->assertEquals(true, $subject->isCurrency('EUR'));
        $this->assertEquals(false, $subject->isCurrency('GBP'));
        $this->assertEquals(false, $subject->isCurrency('USD'));
        $this->assertEquals(false, $subject->isCurrency('CAD'));
        $this->assertEquals(false, $subject->isCurrency('AUS'));
        $this->assertEquals(false, $subject->isCurrency('CHF'));
        $this->assertEquals(false, $subject->isCurrency('ZAR'));

        $subject = new Currency('AUS');

        $this->assertEquals(false, $subject->isCurrency('EUR'));
        $this->assertEquals(false, $subject->isCurrency('GBP'));
        $this->assertEquals(false, $subject->isCurrency('USD'));
        $this->assertEquals(false, $subject->isCurrency('CAD'));
        $this->assertEquals(true, $subject->isCurrency('AUS'));
        $this->assertEquals(false, $subject->isCurrency('CHF'));
        $this->assertEquals(false, $subject->isCurrency('ZAR'));
    }
}
