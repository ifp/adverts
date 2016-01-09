<?php

namespace IFP\Adverts;

class Curl
{
    private $handle;

    public function init($url)
    {
        $this->handle = curl_init($url);
    }

    public function setOpt($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute()
    {
        return curl_exec($this->handle);
    }

    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    public function close()
    {
        curl_close($this->handle);
    }
}
