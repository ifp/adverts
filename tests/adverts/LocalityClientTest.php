<?php

use IFP\Adverts\LocalityClient;

class LocalityClientTest extends PHPUnit_Framework_TestCase
{
    public function testItHitsTheLocalityServer()
    {
        $guzzle_response = Mockery::mock()->shouldReceive('getBody')->andReturn(json_encode([
            'data' => [
                'foo' => 'bar'
            ]
        ]))->getMock();

        $guzzle_client = Mockery::mock()->shouldReceive('request')->with('GET', '/identify', [
            'query' => [
                'country' => 'fr',
                'post_code' => 'foopostcode'
            ]
        ])->andReturn($guzzle_response)->getMock();

        $locality_client = new LocalityClient($guzzle_client);
        $result = $locality_client->retrieveLocality(['country' => 'fr', 'post_code' => 'foopostcode']);

        $this->assertEquals([
            'data' => [
                'foo' => 'bar'
            ]
        ], $result);
    }

    public function testItHitsTheRealLocalityServer()
    {
        $guzzle_client = new \GuzzleHttp\Client([
            'base_uri' => 'https://locality.french-property.com',
            'verify' => false, // Do not check SSL certificates
            'connect_timeout' => 10,
            'timeout' => 10
        ]);

        $locality_client = new LocalityClient($guzzle_client);
        $result = $locality_client->retrieveLocality(['country' => 'fr', 'post_code' => 10400]);

        $this->assertEquals('10400', $result['data']['post_code']);
        $this->assertEquals('Nogent-sur-Seine', $result['data']['commune_name']);
        $this->assertEquals('aube', $result['data']['department_tag']);
        $this->assertEquals(
            [ 'lat' => 48.493, 'lon' => 3.498 ],
            $result['data']['location']
        );
    }
}