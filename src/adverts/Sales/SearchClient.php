<?php

namespace IFP\Adverts\Sales;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use IFP\Adverts\Exceptions\AdvertNotFoundException;
use IFP\Adverts\Exceptions\InvalidApiTokenException;
use IFP\Adverts\Exceptions\InvalidSearchCriteriaException;
use IFP\Adverts\Exceptions\StartPageOutOfBoundsException;
use IFP\Adverts\Exceptions\UnableToConnectToSearchException;

class SearchClient
{
    use QueryStringTrait;

    private $client;
    private $search_path;

    public function __construct(Client $client, $search_path)
    {
        $this->client = $client;
        $this->search_path = $search_path;
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

    public function search($params, $method)
    {
        try {
            $query_string = $this->buildQueryString($params);

            if ($method == 'POST') {

                $params = array_map(function ($param) {
                    if (is_array($param)) {
                        return implode(',', $param);
                    }

                    return $param;
                }, $params);

                $params = [
                    'form_params' => $params,
                    //'debug' => true
                ];

                $response = $this->client->request('POST', $this->search_path, $params);

            } else {
                $response = $this->client->get($this->search_path . '?' . $query_string
//                , ['debug' => true]
                );
            }

//            dd(json_decode((string)$response->getBody(), true));

            return json_decode((string)$response->getBody(), true);
        } catch (ConnectException $e) {
            throw new UnableToConnectToSearchException;

        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 400:
                    throw new InvalidSearchCriteriaException($e);
                    break;
                case 401:
                    throw new InvalidApiTokenException;
                    break;
                case 404:
                    $this->handle404($e);
                    break;
                default:
                    throw $e;
            }
        }
    }

    private function handle404($e)
    {
        $errors = json_decode((string) $e->getResponse()->getBody(), true)['errors'];

        foreach ($errors as $error) {
            if ($error['title'] === 'Start Page Out Of Bounds') {
                throw new StartPageOutOfBoundsException($error);
            }
        }

        throw $e;
    }
}
