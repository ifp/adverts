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
        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com', $this->root->url() . '/foo.txt');

        $this->assertEquals('foo', $data_feed_downloader->data());
    }

    public function testTheLastDownloadedFileCanBeRetrievedFromTheFileSystemIfTheLiveDataFileWasNotDownloaded()
    {
        $this->curl->shouldReceive('getInfo')->andReturn(404);
        vfsStream::newFile('foo.txt', 0755)
            ->withContent('bar')
            ->at($this->root);

        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com', $this->root->url() . '/foo.txt');

        $this->assertEquals('bar', $data_feed_downloader->data());
    }

//    public function testTheDownloadedFileCanBeWrittenToDisk()
//    {
//        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com', $this->root->url() . '/foo.txt');
//
//        $this->assertTrue($data_feed_downloader->saveDownloadedFile('bar'));
//    }

//    public function testExceptionThrownWhenTheDownloadedFileCannotBeWrittenToDisk()
//    {
//        vfsStream::newFile('foo.txt', 0000)
//            ->withContent('not-writable')
//            ->at($this->root);
//
//        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com', $this->root->url() . '/foo.txt');
//
//        try{
//            $data_feed_downloader->saveDownloadedFile('bar');
//        } catch (UnableToWriteDataToDiskException $e) {
//            return;
//        }
//
//        $this->fail('Exception not thrown');
//    }

//    public function testAFileCanBeReadFromDisk()
//    {
//        vfsStream::newFile('foo.txt', 0755)
//            ->withContent('bar')
//            ->at($this->root);
//
//        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com', $this->root->url() . '/foo.txt');
//
//        $this->assertEquals('bar', $data_feed_downloader->readPreviouslyDownloadedFile());
//    }

//    public function testAnExceptionIsThrownWhenAFileCannotBeReadFromDisk()
//    {
//        $data_feed_downloader = new DataFeedDownloader($this->curl, 'http://www.example.com', $this->root->url() . '/foo.txt');
//
//        try{
//            $data_feed_downloader->readPreviouslyDownloadedFile();
//        } catch (UnableToReadFileFromDiskException $e) {
//            return;
//        }
//
//        $this->fail('Exception not thrown');
//    }

}
