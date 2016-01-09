<?php

namespace IFP\Adverts;

class DataFeedDownloader
{
    private $url;
    private $curl;
    private $data;

    public function __construct(Curl $curl, $url)
    {
        $this->curl = $curl;
        $this->curl->init($url);
        $this->url = $url;
    }

    public function data()
    {
        $this->download();

        return $this->data;
    }

    private function download()
    {
        $this->curl->setOpt(CURLOPT_HEADER, false);
        $this->curl->setOpt(CURLOPT_RETURNTRANSFER, true);

        $this->data = $this->curl->execute();

        $http_code = $this->curl->getInfo(CURLINFO_HTTP_CODE);

        $this->curl->close();

        if($http_code != 200) {
            throw new UnableToDownloadDataException('Unable to download data feed ' . $this->url . ', see exception code for http status code', $http_code);
        }
    }

}
