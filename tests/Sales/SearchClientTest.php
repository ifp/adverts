<?php

use IFP\Adverts\AdvertNotFoundException;
use IFP\Adverts\InvalidApiTokenException;
use IFP\Adverts\InvalidSearchCriteriaException;
use IFP\Adverts\Sales\SearchClient;
use IFP\Adverts\StartPageOutOfBoundsException;

class SearchClientTest extends PHPUnit_Framework_TestCase
{
    public function testItThrowsAnExceptionWhenUsingAnInvalidApiToken()
    {
        $base_url = 'http://search.french-property.app';
        $token = 'invalid-api-token';

        $subject = new SearchClient($base_url, $token);

        try {
            $subject->search([]);
        } catch (InvalidApiTokenException $e) {
            return;
        }

        $this->fail('Search succeeded despite invalid API token.');
    }

    public function testItCanSearchWithNoConstraints()
    {
        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([]);

        $this->assertCount(15, $results['data']);
    }

    public function testItCanSearchWithSimpleValueConstraints()
    {
        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'minimum_price' => 150000,
        ]);

        $this->assertEquals(7635, $results['meta']['results']['total']);
    }

    public function testItCanSearchWithArrayConstraints()
    {
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

        $this->assertEquals(1631, $results['meta']['results']['total']);
    }

    public function testItCanSortTheResults()
    {
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
        $base_url = 'http://search.french-property.app';
        $token = getenv('FPAPI_SEARCH_CLIENT_TOKEN');

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'start_page' => 2000,
            'page_size' => 5,
        ]);

        $this->assertCount(1, $results['data']);
    }

    public function testItThrowsAnExceptionWhenSearchingWithInvalidCriteria()
    {
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

        $subject = new SearchClient($base_url, $token);

        try {
            $subject->search([
                'minimum_price' => '1000000',
                'page_size' => 100,
                'start_page' => 100
            ]);
        } catch (StartPageOutOfBoundsException $e) {
            $this->assertEquals(5, $e->lastPage());
            return;
        }

        $this->fail('Search succeeded despite invalid search criteria.');
    }

    public function testItCanFindAnAdvertById()
    {
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
