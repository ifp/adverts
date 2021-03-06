<?php

use IFP\Adverts\Exceptions\AdvertNotFoundException;
use IFP\Adverts\Exceptions\InvalidApiTokenException;
use IFP\Adverts\Exceptions\InvalidSearchCriteriaException;
use IFP\Adverts\Sales\SearchClient;
use IFP\Adverts\Exceptions\StartPageOutOfBoundsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

use IFP\Adverts\Tests\Helpers\MockeryHelper;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ClientExceptionDouble extends ClientException
{
    private $body;

    /** @param string $body */ //for PhpStorm
    public function __construct($status_code, $body) {
        $this->body = $body;

        $mock_request_interface = Mockery::mock(ResponseInterface::class);
        $mock_request_interface->shouldReceive('getStatusCode')->andReturn($status_code);

        parent::__construct('', Mockery::mock(RequestInterface::class), $mock_request_interface);
    }

    public function getResponse()
    {
        $mock_response = Mockery::mock();
        $mock_response->shouldReceive('getBody')->andReturn($this->body); //('{"errors":[{"title":"Start Page Out Of Bounds","meta":{"total_pages":"foo_page_count"}}]}');
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
        $search_path = 'adverts/sales/search';

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->andThrow(
            new ClientExceptionDouble(401, '{"errors":[{"title":"Start Page Out Of Bounds","meta":{"total_pages":"foo_page_count"}}]}'));

        $subject = new SearchClient($client, $search_path);

        try {
            $subject->search([], 'GET');
        } catch (InvalidApiTokenException $e) {
            return;
        }

        $this->fail('Search succeeded despite invalid API token.');
    }

    public function testItCanSearchWithNoConstraints()
    {
        $search_path = 'adverts/sales/search';

        $client_response = Mockery::mock();
        $client_response->shouldReceive('getBody')->andReturn('["foodata"]');
        $client = Mockery::mock(Client::class);

        $subject = new SearchClient($client, $search_path);

        $client->shouldReceive('get')->with(MockeryHelper::expectedParameterContains('/search'))->andReturn($client_response);

        $subject->search([], 'GET');
    }

    public function testItCanSearchWithSimpleValueConstraints()
    {
        $search_path = 'adverts/sales/search';

        $response = Mockery::mock('response', ['getBody' => '']);
        $client = Mockery::spy(Client::class, ['get' => $response]);

        $subject = new SearchClient($client, $search_path);

        $subject->search([
            'minimum_price' => 150000,
        ], 'GET');

        $client->shouldHaveReceived("get")->with(MockeryHelper::expectedParameterEquals('adverts/sales/search?minimum_price=150000'));
    }

    public function testItCanSearchWithArrayConstraints()
    {
        $search_path = 'adverts/sales/search';

        $response = Mockery::mock('response', ['getBody' => '']);
        $client = Mockery::spy(Client::class, ['get' => $response]);
        $subject = new SearchClient($client, $search_path);

        $subject->search([
            'keywords_all' => ['bar', 'full'],
        ], 'GET');

        $client->shouldHaveReceived("get")->with(MockeryHelper::expectedParameterEquals('adverts/sales/search?keywords_all=bar,full'));
    }

    public function testItCanSearchWithGeoConstraints()
    {
        $search_path = 'adverts/sales/search';

        $response = Mockery::mock('response', ['getBody' => '']);
        $client = Mockery::spy(Client::class, ['get' => $response]);
        $subject = new SearchClient($client, $search_path);

        $subject->search([
            'geo' => [
                'lat' => '47.385781',
                'lon' => '3.221313',
                'distance' => '100km',
            ]
        ], 'GET');

        $client->shouldHaveReceived("get")->with(MockeryHelper::expectedParameterEquals('adverts/sales/search?geo[lat]=47.385781&geo[lon]=3.221313&geo[distance]=100km'));
    }

    public function testItCanSortTheResults()
    {
        $search_path = 'adverts/sales/search';

        $response = Mockery::mock('response', ['getBody' => '']);
        $client = Mockery::spy(Client::class, ['get' => $response]);
        $subject = new SearchClient($client, $search_path);

        $subject->search([
            'sort_by' => 'price',
            'sort_direction' => 'desc',
            'maximum_price' => 17900000,
        ], 'GET');

        $client->shouldHaveReceived("get")->with(MockeryHelper::expectedParameterEquals('adverts/sales/search?sort_by=price&sort_direction=desc&maximum_price=17900000'));
    }

    public function testItCanSpecifyPageSizeAndStartPage()
    {
        $search_path = 'adverts/sales/search';

        $response = Mockery::mock('response', ['getBody' => '']);
        $client = Mockery::spy(Client::class, ['get' => $response]);
        $subject = new SearchClient($client, $search_path);

        $subject->search([
            'start_page' => 2000,
            'page_size' => 5,
        ], 'GET');

        $client->shouldHaveReceived("get")->with(MockeryHelper::expectedParameterEquals('adverts/sales/search?start_page=2000&page_size=5'));
    }

    public function testItThrowsAnExceptionWhenSearchingWithInvalidCriteria2()
    {
        $search_path = 'adverts/sales/search';

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->with(MockeryHelper::expectedParameterEquals('adverts/sales/search?minimum_price=bananas'))->andThrow(
            new ClientExceptionDouble(400, '{"errors":[{"title":"fooerror","meta":{}}]}'));

        $subject = new SearchClient($client, $search_path);

        try {
            $subject->search([
                'minimum_price' => 'bananas',
            ], 'GET');
        } catch (InvalidSearchCriteriaException $e) {
            $this->assertCount(1, $e->getErrors());
            return;
        }

        $this->fail('Search succeeded despite invalid search criteria.');
    }

    public function testItThrowsAnExceptionWhenTheSearchEngineReturnsA404()
    {
        $search_path = 'adverts/sales/search';

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->andThrow( new ClientExceptionDouble(404, '{"errors":[{"title":"Start Page Out Of Bounds","meta":{"total_pages":"foo_page_count"}}]}') );

        $subject = new SearchClient($client, $search_path);

        try {
            $subject->search([
                'minimum_price' => '1000000',
                'page_size' => 100,
                'start_page' => 100
            ], 'GET');
        } catch (StartPageOutOfBoundsException $e) {
            $this->assertEquals('foo_page_count', $e->lastPage());
            return;
        }

        $this->fail('Search succeeded despite invalid search criteria - expected StartPageOutOfBoundsException, no exception thrown');
    }

    public function testItCanFindAnAdvertById()
    {
        $search_path = 'adverts/sales/search';

        $response = Mockery::mock('response', ['getBody' => '']);
        $client = Mockery::spy(Client::class, ['get' => $response]);
        $subject = new SearchClient($client, $search_path);

        $subject->find('leggett-FP-56593DSE53');

        $client->shouldHaveReceived("get")->with(MockeryHelper::expectedParameterEquals('adverts/sales/leggett-FP-56593DSE53'));
    }

    public function testItThrowsAnExceptionWhenAnAdvertCannotBeFoundById()
    {
        $search_path = 'adverts/sales/search';

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->with(MockeryHelper::expectedParameterEquals('adverts/sales/non-existent-id'))
            ->andThrow( new ClientExceptionDouble(404, '{"foo"}') );

        $subject = new SearchClient($client, $search_path);

        try {
            $subject->find('non-existent-id');
        } catch (AdvertNotFoundException $e) {
            return;
        }

        $this->fail('Search succeeded despite invalid search criteria - expected AdvertNotFoundException, no exception thrown');
    }

    public function testItCanFindAnAdvertByReference()
    {
        $search_path = 'adverts/sales/search';

        $response = Mockery::mock('response', ['getBody' => '']);
        $client = Mockery::spy(Client::class, ['get' => $response]);
        $subject = new SearchClient($client, $search_path);

        $subject->search([
            'reference' => 'myref'
        ], 'GET');

        $client->shouldHaveReceived("get")->with(MockeryHelper::expectedParameterEquals('adverts/sales/search?reference=myref'));
    }
}
