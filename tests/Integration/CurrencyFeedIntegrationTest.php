<?php

require_once(dirname(dirname(dirname(__DIR__))) . '/data/src/DataFeedDownloader.php');
require_once(dirname(dirname(dirname(__DIR__))) . '/data/src/Exceptions/UnableToDownloadDataException.php');
require_once(dirname(dirname(dirname(__DIR__))) . '/data/src/Exceptions/UnableToWriteDataToDiskException.php');
require_once(dirname(dirname(dirname(__DIR__))) . '/data/src/Exceptions/UnableToReadFileFromDiskException.php');

use IFP\Data\Curl;
use IFP\Adverts\CurrencyDataValidator;
use IFP\Data\DataFeedDownloader;
use Mockery\Mock;
use org\bovigo\vfs\vfsStream;

class CurrencyFeedIntegrationTest extends PHPUnit_Framework_TestCase
{
    private $curl;
    private $root;

    private $currency_example_feed_data =
       '[{"fromCurrency":"EUR", "toCurrency":"foocurrency", "rate":0.5, "name":"My Foo Currency"},' .
        '{"fromCurrency":"EUR", "toCurrency":"barcurrency", "rate":1.5, "name":"My Bar Currency"},' .
        '{"fromCurrency":"EUR", "toCurrency":"qwertycurrency", "rate":0.5, "name":"My Qwerty Currency"}]';

    public function setUp()
    {
        parent::setUp();
        $this->curl = Mockery::spy(Curl::class);
        $this->root = vfsStream::setup();
    }

    public function testFileIsNotModifiedWhenSomeCurrenciesMissingFromFeed()
    {
        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $this->curl->shouldReceive(['execute' => $this->currency_example_feed_data, 'getInfo' => 200]);

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => new CurrencyDataValidator(['foocurrency', 'barcurrency', 'abccurrency']),
            'bugsnag_client' => Mockery::mock()->shouldReceive('notifyError')->once()->getMock()
        ]);

        $this->assertEquals('bar', $data_feed_downloader->data());
    }

    public function testFileIsUpdatedWhenAllCurrenciesPresentInFeed()
    {
        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $this->curl->shouldReceive(['execute' => $this->currency_example_feed_data, 'getInfo' => 200]);

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => new CurrencyDataValidator(['foocurrency', 'barcurrency', 'qwertycurrency']),
            'bugsnag_client' => Mockery::mock()
        ]);

        $this->assertEquals($this->currency_example_feed_data, $data_feed_downloader->data());
    }

    public function testFileIsUpdatedWhenAllCurrenciesPresentWithSomeExtraOnesInFeed()
    {
        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $this->curl->shouldReceive(['execute' => $this->currency_example_feed_data, 'getInfo' => 200]);

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => new CurrencyDataValidator(['foocurrency', 'barcurrency']),
            'bugsnag_client' => Mockery::mock()
        ]);

        $this->assertEquals($this->currency_example_feed_data, $data_feed_downloader->data());
    }
}
