<?php

use IFP\Adverts\InvalidApiTokenException;
use IFP\Adverts\InvalidSearchCriteriaException;
use IFP\Adverts\Sales\SearchClient;

class SearchClientTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        //$this->markTestSkipped('Seed ES using testItCanSeedTheSearchEngineForAdvertSaleClientTesting in search.french-property.com');
    }

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
        $token = 'fWLWPfd0NJ62TKiYZLGlVswXu6YsbXkf';

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([]);

        $this->assertCount(5, $results['data']);
    }

    public function testItCanSearchWithSimpleValueConstraints()
    {
        $base_url = 'http://search.french-property.app';
        $token = 'fWLWPfd0NJ62TKiYZLGlVswXu6YsbXkf';

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'minimum_price' => 150000,
        ]);

        $this->assertCount(3, $results['data']);
    }

    public function testItCanSearchWithArrayConstraints()
    {
        $base_url = 'http://search.french-property.app';
        $token = 'fWLWPfd0NJ62TKiYZLGlVswXu6YsbXkf';

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'keywords_en_all' => ['bar', 'full'],
        ]);

        $this->assertCount(2, $results['data']);
    }

    public function testItCanSearchWithGeoConstraints()
    {
        $base_url = 'http://search.french-property.app';
        $token = 'fWLWPfd0NJ62TKiYZLGlVswXu6YsbXkf';

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'geo' => [
                'lat' => '47.385781',
                'lon' => '3.221313',
                'distance' => '100km',
            ]
        ]);

        $this->assertCount(3, $results['data']);
    }

    public function testItCanSortTheResults()
    {
        $base_url = 'http://search.french-property.app';
        $token = 'fWLWPfd0NJ62TKiYZLGlVswXu6YsbXkf';

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'sort_by' => 'price',
            'sort_direction' => 'desc',
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
        $token = 'fWLWPfd0NJ62TKiYZLGlVswXu6YsbXkf';

        $subject = new SearchClient($base_url, $token);

        $results = $subject->search([
            'start_page' => 2,
            'page_size' => 3,
        ]);

        $this->assertCount(2, $results['data']);
    }

    public function testItThrowsAnExceptionWhenSearchingWithInvalidCriteria()
    {
        $base_url = 'http://search.french-property.app';
        $token = 'fWLWPfd0NJ62TKiYZLGlVswXu6YsbXkf';

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
}
