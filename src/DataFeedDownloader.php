<?php

namespace IFP\Adverts;

use Bugsnag_Client;

class DataFeedDownloader
{
    private $url;
    private $curl;
    private $data;
    private $downloaded_file_save_location;
    private $data_validator;
    private $bugsnag_client;

    public function __construct($options) //Curl $curl, $url, $downloaded_file_save_location, $bugsnag_api_key = null)
    {
        $defaults = ['bugsnag_api_key' => null, 'data_validator' => null];
        $options = array_merge($defaults, $options);

        $this->curl = $options['curl'];
        $this->curl->init($options['url']);
        $this->url = $options['url'];
        $this->downloaded_file_save_location = $options['downloaded_file_save_location'];
        $this->data_validator = $options['data_validator'];
        $this->loadBugsnag($options['bugsnag_api_key']);
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
        $this->curl->setOpt(CURLOPT_TIMEOUT, 2);
        $this->curl->setOpt(CURLOPT_CONNECTTIMEOUT, 1);

        $data = $this->curl->execute();

        $error_no = $this->curl->errorNo();

        if ($error_no == 0) {
            $http_code = $this->curl->getInfo(CURLINFO_HTTP_CODE);
        } else {
            throw new UnableToDownloadDataException('Unable to download data feed ' . $this->url . ', curl error number' . $error_no);
        }

        $this->curl->close();

        if($http_code != 200) {
            throw new UnableToDownloadDataException('Unable to download data feed ' . $this->url . ', see exception code for http status code. Last php error message: ' . error_get_last()['message'], $http_code);
        }

        if($this->data_validator !== null) {
            if($this->data_validator->validate($data) == false) {
                throw new UnableToDownloadDataException('Unable to download data feed ' . $this->url . ' as the data is invalid (JSON fields missing, etc)');
            }
        }

        // NOTE currently every test run from lakesfrance root will save the feed as the cache is always cleared
        // Although this only takes 8 seconds extra (21 vs 13 seconds for 95 tests at time of writing, 2016-08-11)
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
