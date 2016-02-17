<?php

use IFP\Adverts\Currency;

class CurrencyTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->subject = new Currency([
            'EUR' => 1,
            'GBP' => 0.72,
            'USD' => 1.09,
            'CAD' => 1.49,
            'AUD' => 1.5,
            'CHF' => 1.08,
            'ZAR' => 16.93,
        ]);
    }

    public function testTheCurrencyIsSetToEurosByDefault()
    {
        $this->assertEquals('EUR', $this->subject->code());
        $this->assertEquals('€', $this->subject->symbol());
    }

    public function testItCanSetTheCurrencyToEurosAndReturnTheEuroCodeAndHtmlEntitySymbol()
    {
        $this->subject->setCurrency('EUR');

        $this->assertEquals('EUR', $this->subject->code());
        $this->assertEquals('€', $this->subject->symbol());
    }

    public function testItCanSetTheCurrencyToBritishPoundsAndReturnTheBritishPoundCodeAndHtmlEntitySymbol()
    {
        $this->subject->setCurrency('GBP');

        $this->assertEquals('GBP', $this->subject->code());
        $this->assertEquals('£', $this->subject->symbol());
    }

    public function testItCanSetTheCurrencyToUsDollarsAndReturnTheUsDollarCodeAndHtmlEntitySymbol()
    {
        $this->subject->setCurrency('USD');

        $this->assertEquals('USD', $this->subject->code());
        $this->assertEquals('$', $this->subject->symbol());
    }

    public function testItCanSetTheCurrencyToCanadianDollarsAndReturnTheCanadianDollarCodeAndHtmlEntitySymbol()
    {
        $this->subject->setCurrency('CAD');

        $this->assertEquals('CAD', $this->subject->code());
        $this->assertEquals('C$', $this->subject->symbol());
    }

    public function testItCanSetTheCurrencyToAustralianDollarsAndReturnTheAustralianDollarCodeAndHtmlEntitySymbol()
    {
        $this->subject->setCurrency('AUD');

        $this->assertEquals('AUD', $this->subject->code());
        $this->assertEquals('A$', $this->subject->symbol());
    }

    public function testItCanSetTheCurrencyToSwissFrancAndReturnTheSwissFrancCodeAndHtmlEntitySymbol()
    {
        $this->subject->setCurrency('CHF');

        $this->assertEquals('CHF', $this->subject->code());
        $this->assertEquals('Fr.', $this->subject->symbol());
    }

    public function testItCanSetTheCurrencyToSouthAfricanRandAndReturnTheSouthAfricanRandCodeAndHtmlEntitySymbol()
    {
        $this->subject->setCurrency('ZAR');

        $this->assertEquals('ZAR', $this->subject->code());
        $this->assertEquals('R', $this->subject->symbol());
    }

    public function testOnlyConfiguredCurrenciesCanBeSet()
    {
        $this->subject->setCurrency('foo');

        $this->assertEquals('EUR', $this->subject->code());

        $this->subject->setCurrency('USD');

        $this->assertEquals('USD', $this->subject->code());

        $this->subject->setCurrency(null);

        $this->assertEquals('USD', $this->subject->code());
    }

    public function testItCanIdentifyWhetherACurrencyProvidedIsTheCurrencyThatHasBeenSet()
    {
        $this->assertEquals(true, $this->subject->isCurrency('EUR'));
        $this->assertEquals(false, $this->subject->isCurrency('GBP'));
        $this->assertEquals(false, $this->subject->isCurrency('USD'));
        $this->assertEquals(false, $this->subject->isCurrency('CAD'));
        $this->assertEquals(false, $this->subject->isCurrency('AUD'));
        $this->assertEquals(false, $this->subject->isCurrency('CHF'));
        $this->assertEquals(false, $this->subject->isCurrency('ZAR'));

        $this->subject->setCurrency('AUD');

        $this->assertEquals(false, $this->subject->isCurrency('EUR'));
        $this->assertEquals(false, $this->subject->isCurrency('GBP'));
        $this->assertEquals(false, $this->subject->isCurrency('USD'));
        $this->assertEquals(false, $this->subject->isCurrency('CAD'));
        $this->assertEquals(true, $this->subject->isCurrency('AUD'));
        $this->assertEquals(false, $this->subject->isCurrency('CHF'));
        $this->assertEquals(false, $this->subject->isCurrency('ZAR'));
    }

    public function testItCanIdentifyWhetherACurrencyProvidedIsNotTheCurrencyThatHasBeenSet()
    {
        $this->assertEquals(false, $this->subject->isNotCurrency('EUR'));
        $this->assertEquals(true, $this->subject->isNotCurrency('GBP'));
        $this->assertEquals(true, $this->subject->isNotCurrency('USD'));
        $this->assertEquals(true, $this->subject->isNotCurrency('CAD'));
        $this->assertEquals(true, $this->subject->isNotCurrency('AUD'));
        $this->assertEquals(true, $this->subject->isNotCurrency('CHF'));
        $this->assertEquals(true, $this->subject->isNotCurrency('ZAR'));

        $this->subject->setCurrency('AUD');

        $this->assertEquals(true, $this->subject->isNotCurrency('EUR'));
        $this->assertEquals(true, $this->subject->isNotCurrency('GBP'));
        $this->assertEquals(true, $this->subject->isNotCurrency('USD'));
        $this->assertEquals(true, $this->subject->isNotCurrency('CAD'));
        $this->assertEquals(false, $this->subject->isNotCurrency('AUD'));
        $this->assertEquals(true, $this->subject->isNotCurrency('CHF'));
        $this->assertEquals(true, $this->subject->isNotCurrency('ZAR'));
    }

    public function testItCanConvertIntoEurosFromEuros()
    {
        $this->assertEquals(100000, $this->subject->convertFromEuros(100000));
    }

    public function testItCanConvertIntoCurrenciesFromEuros()
    {
        $this->subject->setCurrency('GBP');
        $this->assertEquals(72000, $this->subject->convertFromEuros(100000));

        $this->subject->setCurrency('USD');
        $this->assertEquals(109000, $this->subject->convertFromEuros(100000));

        $this->subject->setCurrency('CAD');
        $this->assertEquals(149000, $this->subject->convertFromEuros(100000));

        $this->subject->setCurrency('AUD');
        $this->assertEquals(150000, $this->subject->convertFromEuros(100000));

        $this->subject->setCurrency('CHF');
        $this->assertEquals(108000, $this->subject->convertFromEuros(100000));

        $this->subject->setCurrency('ZAR');
        $this->assertEquals(1693000, $this->subject->convertFromEuros(100000));
    }

    public function testItCanConvertIntoCurrenciesFromEurosAndRoundNumberDownToNearestInteger()
    {
        $this->subject->setCurrency('GBP');
        $this->assertEquals(72002, $this->subject->convertFromEuros(100003));
        $this->assertEquals(100859, $this->subject->convertFromEuros(140083));
    }

    public function testItCanConvertIntoEurosFromCurrencies()
    {
        $this->subject->setCurrency('GBP');
        $this->assertEquals(138889, $this->subject->convertToEuros(100000));

        $this->subject->setCurrency('USD');
        $this->assertEquals(91743, $this->subject->convertToEuros(100000));

        $this->subject->setCurrency('CAD');
        $this->assertEquals(67114, $this->subject->convertToEuros(100000));

        $this->subject->setCurrency('AUD');
        $this->assertEquals(66667, $this->subject->convertToEuros(100000));

        $this->subject->setCurrency('CHF');
        $this->assertEquals(92593, $this->subject->convertToEuros(100000));

        $this->subject->setCurrency('ZAR');
        $this->assertEquals(5907, $this->subject->convertToEuros(100000));
    }

    public function testItCanConvertInto0FromInvalidValues()
    {
        $this->subject->setCurrency('GBP');
        $this->assertEquals(0, $this->subject->convertFromEuros());
        $this->assertEquals(0, $this->subject->convertFromEuros(null));
        $this->assertEquals(0, $this->subject->convertFromEuros('foo'));
    }

    public function testItFormatsThePriceForDisplayInEuros()
    {
        $this->assertEquals('€125,000', $this->subject->format(125000));
    }

    public function testItFormatsThePriceForDisplayInBritishPounds()
    {
        $this->assertEquals('€100,000 (~£72,000)', $this->subject->format(100000, ['GBP']));
    }

    public function testItFormatsThePriceForDisplayInUsDollars()
    {
        $this->assertEquals('€100,000 (~$109,000)', $this->subject->format(100000, ['USD']));
    }

    public function testItFormatsThePriceForDisplayInCanadianDollars()
    {
        $this->assertEquals('€100,000 (~C$149,000)', $this->subject->format(100000, ['CAD']));
    }

    public function testItFormatsThePriceForDisplayInAustralianDollars()
    {
        $this->assertEquals('€100,000 (~A$150,000)', $this->subject->format(100000, ['AUD']));
    }

    public function testItFormatsThePriceForDisplayInSwissFrancs()
    {
        $this->assertEquals('€100,000 (~Fr.108,000)', $this->subject->format(100000, ['CHF']));
    }

    public function testItFormatsThePriceForDisplayInSouthAfricanRand()
    {
        $this->assertEquals('€100,000 (~R1,693,000)', $this->subject->format(100000, ['ZAR']));
    }

    public function testItFormatsThePriceForDisplayInMultipleCurrencies()
    {
        $this->assertEquals('€100,000 (~£72,000) (~$109,000) (~C$149,000) (~A$150,000) (~Fr.108,000) (~R1,693,000)',
            $this->subject->format(100000, ['GBP', 'USD', 'CAD', 'AUD', 'CHF', 'ZAR']));
    }

    public function testItFormatsThePriceForDisplayInMultipleCurrenciesIncludingTheCurrencyItIsSetTo()
    {
        $this->subject->setCurrency('GBP');

        $this->assertEquals('€100,000 (~£72,000) (~$109,000) (~C$149,000) (~A$150,000) (~Fr.108,000) (~R1,693,000)',
            $this->subject->format(100000, ['GBP', 'USD', 'CAD', 'AUD', 'CHF', 'ZAR']));
    }

    public function testItFormatsThePriceForDisplayInCurrencyThatItIsSetToByDefault()
    {
        $this->subject->setCurrency('ZAR');

        $this->assertEquals('€100,000 (~R1,693,000)', $this->subject->format(100000));
    }
}
