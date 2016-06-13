<?php

use IFP\Adverts\AdvertNotFoundException;
use IFP\Adverts\InvalidApiTokenException;
use IFP\Adverts\InvalidSearchCriteriaException;
use IFP\Adverts\Sales\SearchClient;
use IFP\Adverts\StartPageOutOfBoundsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ClientExceptionDouble extends ClientException
{
    public function __construct($status_code) {
        $mock_request_interface = Mockery::mock(ResponseInterface::class);
        $mock_request_interface->shouldReceive('getStatusCode')->andReturn($status_code);

        parent::__construct('', Mockery::mock(RequestInterface::class), $mock_request_interface);
    }

    public function getResponse()
    {
        $mock_response = Mockery::mock();
        $mock_response->shouldReceive('getBody')->andReturn('{"errors":[{"title":"Start Page Out Of Bounds","meta":{"total_pages":"foo_page_count"}}]}');
        return $mock_response;
    }
}

class SearchClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testItThrowsAnExceptionWhenUsingAnInvalidApiToken()
    {
        $base_url = 'http://search.french-property.app';
        $token = 'invalid-api-token';

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->andThrow( new ClientExceptionDouble(401) );

        $subject = new SearchClient($client, $base_url, $token);

        try {
            $subject->search([]);
        } catch (InvalidApiTokenException $e) {
            return;
        }

        $this->fail('Search succeeded despite invalid API token.');
    }

    public function testItCanSearchWithNoConstraints()
    {
        $base_url = 'https://search.foo.bar';
        $token = 'foobartoken';

        $client_response = Mockery::mock();
        $client_response->shouldReceive('getBody')->andReturn('{"data":[1,2,3,4,5]}');

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->with(Mockery::on(function($request_url) {
            if(strpos($request_url, '/search') === false) {
                throw new Exception('Client mock: SearchClient requested the wrong URL for a search ("/search" not found)');
            }
            return true;
        }))->andReturn($client_response);

        $subject = new SearchClient($client, $base_url, $token);

        $results = $subject->search([]);

        $this->assertCount(5, $results['data']);
    }

    public function testItCanSearchWithSimpleValueConstraints()
    {
        $this->markTestIncomplete();

        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'minimum_price' => 150000,
        ]);

        $this->assertEquals(7636, $results['meta']['results']['total'], 'ensure seeded_test_adverts index has 997 adverts - http://192.168.10.10:9200/_cat/indices?v');
    }

    public function testItCanSearchWithArrayConstraints()
    {
        $this->markTestIncomplete();

        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'keywords_en_all' => ['bar', 'full'],
        ]);

        $this->assertEquals(75, $results['meta']['results']['total']);
    }

    public function testItCanSearchWithGeoConstraints()
    {
        $this->markTestIncomplete();

        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'geo' => [
                'lat' => '47.385781',
                'lon' => '3.221313',
                'distance' => '100km',
            ]
        ]);

        $this->assertEquals(1632, $results['meta']['results']['total'], 'ensure seeded_test_adverts index has 997 adverts - http://192.168.10.10:9200/_cat/indices?v');
    }

    public function testItCanSortTheResults()
    {
        $this->markTestIncomplete();

        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'sort_by' => 'price',
            'sort_direction' => 'desc',
            'maximum_price' => 17900000,
        ]);

        $this->assertTrue($results['data'][0]['property']['price']['amount'] > $results['data'][1]['property']['price']['amount']);
        $this->assertTrue($results['data'][1]['property']['price']['amount'] > $results['data'][2]['property']['price']['amount']);
        $this->assertTrue($results['data'][2]['property']['price']['amount'] > $results['data'][3]['property']['price']['amount']);
        $this->assertTrue($results['data'][3]['property']['price']['amount'] > $results['data'][4]['property']['price']['amount']);
        $this->assertTrue($results['data'][4]['property']['price']['amount'] > $results['data'][5]['property']['price']['amount']);
    }

    public function testItCanSpecifyPageSizeAndStartPage()
    {
        $this->markTestIncomplete();

        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'start_page' => 2000,
            'page_size' => 5,
        ]);

        $this->assertCount(2, $results['data'], 'ensure seeded_test_adverts index has 997 adverts - http://192.168.10.10:9200/_cat/indices?v');
    }

    public function testItThrowsAnExceptionWhenSearchingWithInvalidCriteria()
    {
        $this->markTestIncomplete();

        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        try {
            $subject->search([
                'minimum_price' => 'bananas',
            ]);
        } catch (InvalidSearchCriteriaException $e) {
            $this->assertCount(1, $e->getErrors());
            return;
        }

        $this->fail('Search succeeded despite invalid search criteria.');
    }

    public function testItThrowsAnExceptionWhenTheSearchEngineReturnsA404()
    {
        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->andThrow( new ClientExceptionDouble(404) );

        $subject = new SearchClient($client, $base_url, $token);

        $subject = new SearchClient($client, $base_url, $token);

        try {
            $subject->search([
                'minimum_price' => '1000000',
                'page_size' => 100,
                'start_page' => 100
            ]);
        } catch (StartPageOutOfBoundsException $e) {
            $this->assertEquals('foo_page_count', $e->lastPage());
            return;
        }

        $this->fail('Search succeeded despite invalid search criteria.');
    }

    public function testItCanFindAnAdvertById()
    {
        $this->markTestIncomplete();

        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $result = $subject->find('leggett-FP-56593DSE53');

        $this->assertEquals('FP-56593DSE53', $result['data']['advert']['reference']);
        $this->assertEquals("Stunning renovation with plenty of original features, fully furnished with pool, leisure lake and enclosed pasture", $result['data']['property']['title_en']);
        $this->assertEquals("Superb renovation to create a 4 bedroom family house with plenty of downstairs living space and large kitchen with large swimming pool and patio area to the rear, a leisure lake for fishing (approx 900m2) and a large enclosed field of about 2 acres. Is within a few km of the centre of lovely Cosse le Vivien and about 20km to the centre of the city of Laval to the North. Ideally placed for exploring the Mayenne and Brittany to the West this property would make a great family home or holiday property for personal use and/or rental", $result['data']['property']['description_en']);
        $this->assertEquals("France, Pays de la Loire, Mayenne (53), Cossé-le-Vivien", $result['data']['property']['geo']['full_location']);
        $this->assertEquals("224700", $result['data']['property']['price']['amount']);
        $this->assertEquals("4", $result['data']['property']['room_totals']['bedrooms']);
        $this->assertEquals('148', $result['data']['property']['floor_area']['size']);
        $this->assertEquals('13202', $result['data']['property']['land_area']['size']);
        $this->assertEquals(['swimming_pool'], $result['data']['property']['attributes']['external_features']);
    }

    public function testItThrowsAnExceptionWhenAnAdvertCannotBeFoundById()
    {
        $this->markTestIncomplete();

        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        try {
            $subject->find('non-existent-id');
        } catch (AdvertNotFoundException $e) {
            return;
        }

        $this->fail('Search succeeded despite invalid search criteria.');
    }

}
