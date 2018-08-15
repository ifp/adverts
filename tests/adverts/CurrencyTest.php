<?php

use IFP\Adverts\Currency;

class CurrencyTest extends PHPUnit_Framework_TestCase
{
    private $subject;

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

    public function testItCanConvertIntoAllCurrencies()
    {
        $this->subject->setCurrency('EUR');

        $assertion = [
            'EUR' => 100000,
            'GBP' => 72000,
            'USD' => 109000,
            'CAD' => 149000,
            'AUD' => 150000,
            'CHF' => 108000,
            'ZAR' => 1693000,
        ];

        $this->assertEquals($assertion, $this->subject->convertCurrenciesFromEuros(100000));
    }

    public function testItCanConvertAndFormatIntoAllCurrencies()
    {
        $this->subject->setCurrency('EUR');

        $assertion = [
            'EUR' => '€100,000',
            'GBP' => '£72,000',
            'USD' => '$109,000',
            'CAD' => 'C$149,000',
            'AUD' => 'A$150,000',
            'CHF' => 'Fr.108,000',
            'ZAR' => 'R1,693,000',
        ];

        $this->assertEquals($assertion, $this->subject->formatCurrenciesFromEuros(100000));
    }
}
