<?php

namespace IFP\Adverts;

class LocalityClient
{
    private $guzzle_client;

    public function __construct($guzzle_client)
    {
        $this->guzzle_client = $guzzle_client;
    }

    public function retrieveLocality($options)
    {
        $guzzle_response = $this->guzzle_client->request('GET', '/identify', [
            'query' => $options
        ]);

        return json_decode($guzzle_response->getBody(), true);
    }
}