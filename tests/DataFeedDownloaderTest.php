<?php

use IFP\Adverts\Curl;
use IFP\Adverts\DataFeedDownloader;
//use IFP\Adverts\UnableToDownloadDataException;
//use IFP\Adverts\UnableToReadFileFromDiskException;
//use IFP\Adverts\UnableToWriteDataToDiskException;
use Mockery\Mock;
use org\bovigo\vfs\vfsStream;

class DataFeedDownloaderTest extends PHPUnit_Framework_TestCase
{
    private $curl;
    private $root;

    public function setUp()
    {
        parent::setUp();
        $this->curl = Mockery::spy(Curl::class);
        $this->root = vfsStream::setup();
    }

    public function testTheDataFileCanBeRetrievedAfterDownloading()
    {
        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $this->curl->shouldReceive(['execute' => 'foo', 'getInfo' => 200]);

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => Mockery::mock()->shouldReceive('validate')->andReturn(true)->getMock()
        ]);

        $this->assertEquals('foo', $data_feed_downloader->data());
    }

    public function testTheLastDownloadedFileCanBeRetrievedFromTheFileSystemIfTheLiveDataFileWasNotDownloaded()
    {
        $this->curl->shouldReceive('getInfo')->andReturn(404);

        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => Mockery::mock()->shouldReceive('validate')->andReturn(true)->getMock()
        ]);

        $this->assertEquals('bar', $data_feed_downloader->data());
    }

    public function testTheLastDownloadedFileCanBeRetrievedFromTheFileSystemIfTheLiveDataFileWasNotDownloadedDueToTimeout()
    {
        $this->curl = new Curl();

        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'https://www.french-property.com/timeout.php',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => Mockery::mock()->shouldReceive('validate')->andReturn(true)->getMock()
        ]);

        $this->assertEquals('bar', $data_feed_downloader->data());
    }

    public function testFileIsModifiedWhenDataValidatorIsNull()
    {
        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $this->curl->shouldReceive(['execute' => 'foo', 'getInfo' => 200]);

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            //Validator not passed in - set to null
        ]);

        $this->assertEquals('foo', $data_feed_downloader->data());
    }

    public function testFileIsNotModifiedIfDataValidatorReturnsInvalid()
    {
        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $this->curl->shouldReceive(['execute' => '{foo}', 'getInfo' => 200]);

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => Mockery::mock()->shouldReceive('validate')->with('{foo}')->andReturn(false)->getMock()
        ]);

        $this->assertEquals('bar', $data_feed_downloader->data());
    }
}
