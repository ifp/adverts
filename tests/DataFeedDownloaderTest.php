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
            'data_validator' => Mockery::mock()->shouldReceive('validate')->andReturn(true)->getMock(),
            'bugsnag_client' => Mockery::mock()
        ]);

        $this->assertEquals('foo', $data_feed_downloader->data());
    }

    public function testTheLastDownloadedFileCanBeRetrievedFromTheFileSystemIfTheLiveDataFileWasNotDownloaded()
    {
        $this->curl->shouldReceive('getInfo')->andReturn(404);

        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $bugsnag_client_fake = new BugsnagClientFake();

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => Mockery::mock()->shouldReceive('validate')->andReturn(true)->getMock(),
            'bugsnag_client' => $bugsnag_client_fake
        ]);

        $this->assertEquals('bar', $data_feed_downloader->data());
        $this->assertEquals(['DataFileLoadedFromDisk'], $bugsnag_client_fake->recievedErrors());
    }

    public function testTheLastDownloadedFileCanBeRetrievedFromTheFileSystemIfTheLiveDataFileWasNotDownloadedDueToTimeout()
    {
        $this->curl = new Curl();

        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $bugsnag_client_fake = new BugsnagClientFake();

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'https://www.french-property.com/timeout.php',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => Mockery::mock()->shouldReceive('validate')->andReturn(true)->getMock(),
            'bugsnag_client' => $bugsnag_client_fake
        ]);

        $this->assertEquals('bar', $data_feed_downloader->data());
        $this->assertEquals(['DataFileLoadedFromDisk'], $bugsnag_client_fake->recievedErrors());

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
            'bugsnag_client' => Mockery::mock()
        ]);

        $this->assertEquals('foo', $data_feed_downloader->data());
    }

    public function testFileIsNotModifiedIfDataValidatorReturnsInvalid()
    {
        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $this->curl->shouldReceive(['execute' => '{foo}', 'getInfo' => 200]);

        $bugsnag_client_fake = new BugsnagClientFake();

        $data_feed_downloader = new DataFeedDownloader([
            'curl' => $this->curl,
            'url' => 'http://www.example.com',
            'downloaded_file_save_location' => $this->root->url() . '/foo.txt',
            'data_validator' => Mockery::mock()->shouldReceive('validate')->with('{foo}')->andReturn(false)->getMock(),
            'bugsnag_client' => $bugsnag_client_fake
        ]);

        $this->assertEquals('bar', $data_feed_downloader->data());
        $this->assertEquals(['DataFileLoadedFromDisk'], $bugsnag_client_fake->recievedErrors());
    }
}

class BugsnagClientFake
{
    private $recieved_errors;

    public function __construct()
    {
        $this->recieved_errors = [];
    }

    public function notifyError($error_type)
    {
        $this->recieved_errors[] = $error_type;
    }

    public function recievedErrors()
    {
        return $this->recieved_errors;
    }
}