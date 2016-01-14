<?php

namespace IFP\Adverts;

class DataFeedDownloader
{
    private $url;
    private $curl;
    private $data;
    private $downloaded_file_save_location;

    public function __construct(Curl $curl, $url, $downloaded_file_save_location)
    {
        $this->curl = $curl;
        $this->curl->init($url);
        $this->url = $url;
        $this->downloaded_file_save_location = $downloaded_file_save_location;
    }

    public function data()
    {
        $this->data = $this->download();

        return $this->data;
    }

    private function download()
    {
        $this->curl->setOpt(CURLOPT_HEADER, false);
        $this->curl->setOpt(CURLOPT_RETURNTRANSFER, true);

        $data = $this->curl->execute();

        $http_code = $this->curl->getInfo(CURLINFO_HTTP_CODE);

        $this->curl->close();

        if($http_code != 200) {
            throw new UnableToDownloadDataException('Unable to download data feed ' . $this->url . ', see exception code for http status code. Last php error message: ' . error_get_last()['message'], $http_code);
        }

        return $data;
    }

    public function saveDownloadedFile($data)
    {
        if (false === @file_put_contents($this->downloaded_file_save_location, $data)) {
            throw new UnableToWriteDataToDiskException('Failure while storing downloaded file : ' . error_get_last()['message']);
        }

        return true;
    }

    public function readPreviouslyDownloadedFile()
    {
        $data = @file_get_contents($this->downloaded_file_save_location);

        if ($data === false) {
            throw new UnableToReadFileFromDiskException('Failure while reading downloaded file : ' . error_get_last()['message']);
        }

        return $data;
    }
}
