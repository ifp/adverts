<?php

use IFP\Adverts\Curl;
use IFP\Adverts\CurrencyDataValidator;
use IFP\Adverts\DataFeedDownloader;
//use IFP\Adverts\UnableToDownloadDataException;
//use IFP\Adverts\UnableToReadFileFromDiskException;
//use IFP\Adverts\UnableToWriteDataToDiskException;
use Mockery\Mock;
use org\bovigo\vfs\vfsStream;


class CurrencyDataValidatorTest extends PHPUnit_Framework_TestCase
{
    /** @var  CurrencyDataValidator */
    private $currency_data_validator;

    public function setUp()
    {
        parent::setUp();
    }

    public function testSimpleNonJsonStringIsInvalid()
    {
        $this->currency_data_validator = new CurrencyDataValidator([]);
        $this->assertEquals(false, $this->currency_data_validator->validate('foo'));
    }

    public function testSingleCurrencyValidates()
    {
        $this->currency_data_validator = new CurrencyDataValidator(['foocurrency']);

        $data = '[{"fromCurrency":"EUR", "toCurrency":"foocurrency", "rate":0.5, "name":"My Foo Currency"}]';

        $this->assertEquals(true, $this->currency_data_validator->validate($data));
    }

    public function testWrongSingleCurrencyFailsToValidate()
    {
        $this->currency_data_validator = new CurrencyDataValidator(['currency1']);

        $data = '[{"fromCurrency":"EUR", "toCurrency":"currency2", "rate":0.5, "name":"Did you want currency1 instead?"}]';

        $this->assertEquals(false, $this->currency_data_validator->validate($data));
    }

    public function testMultipleCurrenciesValidateWhenAllArePresent()
    {
        $this->currency_data_validator = new CurrencyDataValidator(['foocurrency', 'barcurrency', 'qwertycurrency']);

        $data = '[{"fromCurrency":"EUR", "toCurrency":"foocurrency", "rate":0.5, "name":"My Foo Currency"},' .
                 '{"fromCurrency":"EUR", "toCurrency":"barcurrency", "rate":1.5, "name":"My Bar Currency"},' .
                 '{"fromCurrency":"EUR", "toCurrency":"qwertycurrency", "rate":0.5, "name":"My Qwerty Currency"}]';

        $this->assertEquals(true, $this->currency_data_validator->validate($data));
    }

    public function testMultipleCurrenciesFailToValidateWhenOneIsMissing()
    {
        $this->currency_data_validator = new CurrencyDataValidator(['foocurrency', 'barcurrency', 'qwertycurrency', 'gold_coins']);

        $data = '[{"fromCurrency":"EUR", "toCurrency":"foocurrency", "rate":0.5, "name":"My Foo Currency"},' .
            '{"fromCurrency":"EUR", "toCurrency":"barcurrency", "rate":1.5, "name":"My Foo Currency"},' .
            '{"fromCurrency":"EUR", "toCurrency":"qwertycurrency", "rate":0.5, "name":"My Foo Currency"}]';

        $this->assertEquals(false, $this->currency_data_validator->validate($data));
    }

    public function testMultipleCurrenciesValidateWhenAllArePresentWithSomeExtras()
    {
        $this->currency_data_validator = new CurrencyDataValidator(['foocurrency', 'barcurrency']);

        $data = '[{"fromCurrency":"EUR", "toCurrency":"foocurrency", "rate":0.5, "name":"My Foo Currency"},' .
            '{"fromCurrency":"EUR", "toCurrency":"barcurrency", "rate":1.5, "name":"My Foo Currency"},' .
            '{"fromCurrency":"EUR", "toCurrency":"qwertycurrency", "rate":0.5, "name":"My Foo Currency"}]';

        $this->assertEquals(true, $this->currency_data_validator->validate($data));
    }
}
