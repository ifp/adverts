<?php

namespace IFP\Adverts;

use GuzzleHttp\Exception\ClientException;
use IFP\Adverts\Exceptions\InvalidApiTokenException;

class EnquiriesClient
{
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function send($form_data)
    {
        try {
            $this->client->post('api/enquiries', ['json' => $form_data]);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 401:
                    throw new InvalidApiTokenException;
                    break;
                default:
                    throw $e;
            }
        }
    }
}
