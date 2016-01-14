<?php

namespace IFP\Adverts;

use Bugsnag_Client;

class DataFeedDownloader
{
    private $url;
    private $curl;
    private $data;
    private $downloaded_file_save_location;
    private $bugsnag_client;

    public function __construct(Curl $curl, $url, $downloaded_file_save_location, $bugsnag_api_key = null)
    {
        $this->curl = $curl;
        $this->curl->init($url);
        $this->url = $url;
        $this->downloaded_file_save_location = $downloaded_file_save_location;
        $this->loadBugsnag($bugsnag_api_key);
    }

    public function data()
    {
        try {
            $this->data = $this->download();
        } catch (UnableToDownloadDataException $e) {
            $this->data = $this->readPreviouslyDownloadedFile();
        }

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

        $this->saveDownloadedFile($data);

        return $data;
    }

    private function saveDownloadedFile($data)
    {
        if (false === @file_put_contents($this->downloaded_file_save_location, $data)) {
            $e = new UnableToWriteDataToDiskException('Failure while storing downloaded file : ' . error_get_last()['message']);
            $this->bugsnagNotifyException($e);
            throw $e;
        }

        return true;
    }

    private function readPreviouslyDownloadedFile()
    {
        $data = @file_get_contents($this->downloaded_file_save_location);

        if ($data === false) {
            $e = new UnableToReadFileFromDiskException('Failure while reading downloaded file : ' . error_get_last()['message']);
            $this->bugsnagNotifyException($e);
            throw $e;
        } else {
            $this->bugsnagNotifyError('DataFileLoadedFromDisk', 'Data file not downloaded from ' . $this->url . ', but instead read from filesystem ' . $this->downloaded_file_save_location);
        }

        return $data;
    }

    private function loadBugsnag($bugsnag_api_key)
    {
        $this->bugsnag_client = null;

        if (class_exists('Bugsnag_Client')) {
            $this->bugsnag_client = new Bugsnag_Client($bugsnag_api_key);
        }
    }

    private function bugsnagNotifyException($e)
    {
        if($this->bugsnag_client) {
            $this->bugsnag_client->notifyException($e);
        }
    }

    private function bugsnagNotifyError($error_type, $error_message)
    {
        if($this->bugsnag_client) {
            $this->bugsnag_client->notifyError($error_type, $error_message);
        }
    }
}
