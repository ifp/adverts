<?php

use IFP\Adverts\Curl;
use IFP\Adverts\DataFeedDownloader;
use IFP\Adverts\UnableToDownloadDataException;
use Mockery\Mock;

class DataFeedDownloaderTest extends PHPUnit_Framework_TestCase
{
    private $curl;

    public function setUp()
    {
        parent::setUp();
        $this->curl = Mockery::spy(Curl::class);
    }

    public function testTheDataFeedUrlCanBeSet()
    {
        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com');

        $this->assertEquals('http://www.example.com', $data_feed_downloader->url());
    }

    public function testTheDataFeedCanBeDownloaded()
    {
        $this->curl->shouldReceive('getInfo')->andReturn(200);
        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com');

        try {
            $data_feed_downloader->download();
        } catch (UnableToDownloadDataException $e) {
            print_r($e->getMessage());
            $this->fail('Exception thrown');
        }
        $this->assertEquals(true, true);
    }

    public function testTheDataFeedCanBeRetreivedAfterDownloading()
    {
        $this->curl->shouldReceive(['execute' => 'foo', 'getInfo' => 200]);
        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com');

        $data_feed_downloader->download();

        $this->assertEquals('foo', $data_feed_downloader->data());
    }

    public function testAnExceptionWithTheHttpStatusCodeIsThrownIfTheDataCannotBeDownloaded()
    {
        $this->curl->shouldReceive('getInfo')->andReturn(404);
        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.french-property.com/invalidfilename.txt');

        try {
            $data_feed_downloader->download();
        } catch (UnableToDownloadDataException $e) {
            $this->assertEquals(404, $e->getCode());
            return;
        }

        $this->fail('Exception not thrown');
    }
}
