<?php

use IFP\Adverts\Currency;

class CurrencyTest extends PHPUnit_Framework_TestCase
{
    public function testTheCurrencyIsSetToEurosByDefault()
    {
        $subject = new Currency([]);

        $this->assertEquals('EUR', $subject->code());
        $this->assertEquals('€', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToEurosAndReturnTheEuroCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency([]);
        $subject->setCurrency('EUR');

        $this->assertEquals('EUR', $subject->code());
        $this->assertEquals('€', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToBritishPoundsAndReturnTheBritishPoundCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency([]);
        $subject->setCurrency('GBP');

        $this->assertEquals('GBP', $subject->code());
        $this->assertEquals('£', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToUsDollarsAndReturnTheUsDollarCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency([]);
        $subject->setCurrency('USD');

        $this->assertEquals('USD', $subject->code());
        $this->assertEquals('$', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToCanadianDollarsAndReturnTheCanadianDollarCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency([]);
        $subject->setCurrency('CAD');

        $this->assertEquals('CAD', $subject->code());
        $this->assertEquals('C$', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToAustralianDollarsAndReturnTheAustralianDollarCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency([]);
        $subject->setCurrency('AUD');

        $this->assertEquals('AUD', $subject->code());
        $this->assertEquals('A$', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToSwissFrancAndReturnTheSwissFrancCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency([]);
        $subject->setCurrency('CHF');

        $this->assertEquals('CHF', $subject->code());
        $this->assertEquals('Fr.', $subject->symbol());
    }

    public function testItCanSetTheCurrencyToSouthAfricanRandAndReturnTheSouthAfricanRandCodeAndHtmlEntitySymbol()
    {
        $subject = new Currency([]);
        $subject->setCurrency('ZAR');

        $this->assertEquals('ZAR', $subject->code());
        $this->assertEquals('R', $subject->symbol());
    }

    public function testOnlyConfiguredCurrenciesCanBeSet()
    {
        $subject = new Currency([]);
        $subject->setCurrency('foo');

        $this->assertEquals('EUR', $subject->code());

        $subject->setCurrency('USD');

        $this->assertEquals('USD', $subject->code());

        $subject->setCurrency(null);

        $this->assertEquals('USD', $subject->code());
    }

    public function testItCanIdentifyWhetherACurrencyProvidedIsTheCurrencyThatHasBeenSet()
    {
        $subject = new Currency([]);

        $this->assertEquals(true, $subject->isCurrency('EUR'));
        $this->assertEquals(false, $subject->isCurrency('GBP'));
        $this->assertEquals(false, $subject->isCurrency('USD'));
        $this->assertEquals(false, $subject->isCurrency('CAD'));
        $this->assertEquals(false, $subject->isCurrency('AUD'));
        $this->assertEquals(false, $subject->isCurrency('CHF'));
        $this->assertEquals(false, $subject->isCurrency('ZAR'));

        $subject = new Currency([]);
        $subject->setCurrency('AUD');

        $this->assertEquals(false, $subject->isCurrency('EUR'));
        $this->assertEquals(false, $subject->isCurrency('GBP'));
        $this->assertEquals(false, $subject->isCurrency('USD'));
        $this->assertEquals(false, $subject->isCurrency('CAD'));
        $this->assertEquals(true, $subject->isCurrency('AUD'));
        $this->assertEquals(false, $subject->isCurrency('CHF'));
        $this->assertEquals(false, $subject->isCurrency('ZAR'));
    }

    public function testItCanIdentifyWhetherACurrencyProvidedIsNotTheCurrencyThatHasBeenSet()
    {
        $subject = new Currency([]);

        $this->assertEquals(false, $subject->isNotCurrency('EUR'));
        $this->assertEquals(true, $subject->isNotCurrency('GBP'));
        $this->assertEquals(true, $subject->isNotCurrency('USD'));
        $this->assertEquals(true, $subject->isNotCurrency('CAD'));
        $this->assertEquals(true, $subject->isNotCurrency('AUD'));
        $this->assertEquals(true, $subject->isNotCurrency('CHF'));
        $this->assertEquals(true, $subject->isNotCurrency('ZAR'));

        $subject = new Currency([]);
        $subject->setCurrency('AUD');

        $this->assertEquals(true, $subject->isNotCurrency('EUR'));
        $this->assertEquals(true, $subject->isNotCurrency('GBP'));
        $this->assertEquals(true, $subject->isNotCurrency('USD'));
        $this->assertEquals(true, $subject->isNotCurrency('CAD'));
        $this->assertEquals(false, $subject->isNotCurrency('AUD'));
        $this->assertEquals(true, $subject->isNotCurrency('CHF'));
        $this->assertEquals(true, $subject->isNotCurrency('ZAR'));
    }

    public function testItCanConvertIntoEurosFromEuros()
    {
        $subject = new Currency(['EUR' => 1]);

        $this->assertEquals(100000, $subject->convertFromEuros(100000));
    }

    public function testItCanConvertIntoCurrenciesFromEuros()
    {
        $subject = new Currency([
            'GBP' => 0.72,
            'USD' => 1.09,
            'CAD' => 1.49,
            'AUD' => 1.5,
            'CHF' => 1.08,
            'ZAR' => 16.93,
        ]);

        $subject->setCurrency('GBP');
        $this->assertEquals(72000, $subject->convertFromEuros(100000));

        $subject->setCurrency('USD');
        $this->assertEquals(109000, $subject->convertFromEuros(100000));

        $subject->setCurrency('CAD');
        $this->assertEquals(149000, $subject->convertFromEuros(100000));

        $subject->setCurrency('AUD');
        $this->assertEquals(150000, $subject->convertFromEuros(100000));

        $subject->setCurrency('CHF');
        $this->assertEquals(108000, $subject->convertFromEuros(100000));

        $subject->setCurrency('ZAR');
        $this->assertEquals(1693000, $subject->convertFromEuros(100000));
    }

    public function testItCanConvertIntoCurrenciesFromEurosAndRoundNumberDownToNearestInteger()
    {
        $subject = new Currency([
            'GBP' => 0.72,
        ]);

        $subject->setCurrency('GBP');
        $this->assertEquals(72002, $subject->convertFromEuros(100003));
        $this->assertEquals(100859, $subject->convertFromEuros(140083));
    }

    public function testItCanConvertIntoEurosFromCurrencies()
    {
        $subject = new Currency([
            'GBP' => 0.72,
            'USD' => 1.09,
            'CAD' => 1.49,
            'AUD' => 1.5,
            'CHF' => 1.08,
            'ZAR' => 16.93,
        ]);

        $subject->setCurrency('GBP');
        $this->assertEquals(138889, $subject->convertToEuros(100000));

        $subject->setCurrency('USD');
        $this->assertEquals(91743, $subject->convertToEuros(100000));

        $subject->setCurrency('CAD');
        $this->assertEquals(67114, $subject->convertToEuros(100000));

        $subject->setCurrency('AUD');
        $this->assertEquals(66667, $subject->convertToEuros(100000));

        $subject->setCurrency('CHF');
        $this->assertEquals(92593, $subject->convertToEuros(100000));

        $subject->setCurrency('ZAR');
        $this->assertEquals(5907, $subject->convertToEuros(100000));
    }

    public function testItCanConvertInto0FromInvalidValues()
    {
        $subject = new Currency([
            'GBP' => 0.72,
        ]);

        $subject->setCurrency('GBP');
        $this->assertEquals(0, $subject->convertFromEuros());
        $this->assertEquals(0, $subject->convertFromEuros(null));
        $this->assertEquals(0, $subject->convertFromEuros('foo'));
    }
}
