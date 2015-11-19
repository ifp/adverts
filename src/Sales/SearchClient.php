<?php

namespace IFP\Adverts\Sales;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use IFP\Adverts\AdvertNotFoundException;
use IFP\Adverts\InvalidApiTokenException;
use IFP\Adverts\InvalidSearchCriteriaException;

class SearchClient
{
    private $client;

    public function __construct($base_url, $token)
    {
        $this->client = new Client([
            'base_uri' => $base_url,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);
    }

    public function find($id)
    {
        try {
            $response = $this->client->get('adverts/sales/' . $id);

            return json_decode((string) $response->getBody(), true);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 401:
                    throw new InvalidApiTokenException;
                    break;
                case 404:
                    throw new AdvertNotFoundException($e);
                    break;
                default:
                    throw $e;
            }
        }
    }

    public function search($params)
    {
        try {
            $query_string = $this->buildQueryString($params);
            //var_dump($query_string); exit;

            $response = $this->client->get('adverts/sales/search?' . $query_string);

            return json_decode((string) $response->getBody(), true);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 400:
                    throw new InvalidSearchCriteriaException($e);
                    break;
                case 401:
                    throw new InvalidApiTokenException;
                    break;
                default:
                    throw $e;
            }
        }
    }

    private function buildQueryString($params)
    {
        return implode('&', array_map(function ($key, $value) {
            return $this->buildQueryStringParam($key, $value);
        }, array_keys($params), $params));
    }

    private function buildQueryStringParam($key, $value)
    {
        if (! is_array($value)) {
            return $key . '=' . $value;
        }

        if ($this->isAssoc($value)) {
            return $this->buildAssocParam($key, $value);
        }

        return $this->buildListParam($key, $value);
    }

    private function buildAssocParam($key, $value)
    {
        $key_value_pairs = [];

        foreach ($value as $subkey => $subvalue) {
            $key_value_pairs["{$key}[{$subkey}]"] = $subvalue;
        }

        return $this->buildQueryString($key_value_pairs);
    }

    private function buildListParam($key, $value)
    {
        return $key . '=' . implode(',', $value);
    }

    private function isAssoc($array)
    {
        $keys = array_keys($array);

        foreach ($keys as $key) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }
}

