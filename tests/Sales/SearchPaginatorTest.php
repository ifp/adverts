<?php

use IFP\Adverts\InvalidPaginationDataException;
use IFP\Adverts\Sales\SearchPaginator;

class SearchPaginatorTest extends PHPUnit_Framework_TestCase
{
    public function testPaginatorThrowsWhenGivenNoResultsData()
    {
        $results = [];

        try {
            new SearchPaginator('', [], $results);
        } catch (InvalidPaginationDataException $e) {
            $this->assertAssocArrayValuesContain('`total` was not set or is null.', $e->getErrors());
            $this->assertAssocArrayValuesContain('`starting_from` was not set or is null.', $e->getErrors());
            $this->assertAssocArrayValuesContain('`finishing_at` was not set or is null.', $e->getErrors());
            $this->assertAssocArrayValuesContain('`current_page` was not set or is null.', $e->getErrors());
            $this->assertAssocArrayValuesContain('`total_pages` was not set or is null.', $e->getErrors());
            $this->assertCount(5, $e->getErrors());
            return;
        }

        $this->fail('Pagination succeeded despite invalid pagination criteria.');
    }
    public function testPaginatorThrowsWhenGivenInvalidResultsData()
    {
        $results = [
            "total" => "997.7",
            "starting_from" => null,
            "finishing_at" => 'what is a string doing here?',
            "current_page" => ['or an array!'],
            "total_pages" => 667];

        try {
            new SearchPaginator('', [], $results);
        } catch (InvalidPaginationDataException $e) {
            $this->assertAssocArrayValuesContain('`starting_from` was not set or is null.', $e->getErrors());
            $this->assertAssocArrayValuesContain('`finishing_at` was not numeric.', $e->getErrors());
            $this->assertAssocArrayValuesContain('`current_page` was not numeric.', $e->getErrors());
            $this->assertCount(3, $e->getErrors());
            return;
        }

        $this->fail('Pagination succeeded despite invalid pagination criteria.');
    }

    public function testPaginatorCanSpecifyTotalNumberOfResultsGivenValidResultsData()
    {
        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(9997, $subject->total());
    }

    public function testPaginatorCanSpecifyStartingFromNumberOfPageGivenValidResultsData()
    {
        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(991, $subject->startingFrom());
    }

    public function testPaginatorCanSpecifyFinishingAtNumberOfPageGivenValidResultsData()
    {
        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(1005, $subject->finishingAt());
    }

    public function testPaginatorCanSpecifyCurrentPageNumberGivenValidResultsData()
    {
        $results = [
            "total" => 9997.7,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(67, $subject->currentPage());
    }

    public function testPaginatorCanSpecifyTotalNumberOfPagesGivenValidResultsData()
    {
        $results = [
            "total" => 9997.7,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(667, $subject->totalPages());
    }

    public function testPaginatorProvidesZeroesWhenGivenZeroedResultsData()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 0,
            "total_pages" => 0
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(0, $subject->total());
        $this->assertEquals(0, $subject->startingFrom());
        $this->assertEquals(0, $subject->finishingAt());
        $this->assertEquals(0, $subject->currentPage());
        $this->assertEquals(0, $subject->totalPages());
    }

    public function testItCanTellThatItDoesHaveMorePagesOfResults()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 1,
            "total_pages" => 2
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertTrue($subject->hasMorePages());
    }

    public function testItCanTellThatItDoesHaveLessPagesOfResults()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 3,
            "total_pages" => 0,
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertTrue($subject->hasLessPages());
    }

    public function testItCanRetrieveItsNextPage()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 1,
            "total_pages" => 2,
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(2, $subject->nextPage());
    }

    public function testItCannotRetrieveItsNextPageIfItIsCurrentlyOnTheLastPage()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 4,
            "total_pages" => 4,
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(4, $subject->nextPage());
    }

    public function testItCanRetrieveItsPreviousPage()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 3,
            "total_pages" => 5,
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(2, $subject->previousPage());
    }

    public function testItCannotRetrieveItsPreviousPageIfItIsCurrentlyOnTheFirstPage()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 1,
            "total_pages" => 4,
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(1, $subject->previousPage());
    }

    public function testItCanRetrieveItsFirstPage()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 4,
            "total_pages" => 4,
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(1, $subject->firstPage());
    }

    public function testItCanRetrieveItsLastPage()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 4,
            "total_pages" => 8,
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertEquals(8, $subject->lastPage());
    }

    public function testPaginatorProvidesFirstPageUrlWhenGivenValidData()
    {
        $base_url = '/sale-advert-search';

        $search_criteria = [
            'title_en_any' => 'house with garden',
            'keywords_en_any' => 'swimming pool',
            'minimum_price' => 100000,
            'maximum_price' => 150000,
            'minimum_bedrooms' => 3,
            'minimum_land_size' => 1000,
            'page_size' => 15,
            'start_page' => 67,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $this->assertEquals('/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1', $subject->firstPageUrl());
    }

    public function testPaginatorProvidesLastPageUrlWhenGivenValidData()
    {
        $base_url = '/sale-advert-search';

        $search_criteria = [
            'title_en_any' => 'house with garden',
            'keywords_en_any' => 'swimming pool',
            'minimum_price' => 100000,
            'maximum_price' => 150000,
            'minimum_bedrooms' => 3,
            'minimum_land_size' => 1000,
            'page_size' => 15,
            'start_page' => 67,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $this->assertEquals('/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=667', $subject->lastPageUrl());
    }

    public function testPaginatorProvidesNextPageUrlWhenGivenValidData()
    {
        $base_url = '/sale-advert-search';

        $search_criteria = [
            'title_en_any' => 'house with garden',
            'keywords_en_any' => 'swimming pool',
            'minimum_price' => 100000,
            'maximum_price' => 150000,
            'minimum_bedrooms' => 3,
            'minimum_land_size' => 1000,
            'page_size' => 15,
            'start_page' => 67,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $this->assertEquals('/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=68', $subject->nextPageUrl());
    }

    public function testPaginatorProvidesPreviousPageUrlWhenGivenValidData()
    {
        $base_url = '/sale-advert-search';

        $search_criteria = [
            'title_en_any' => 'house with garden',
            'keywords_en_any' => 'swimming pool',
            'minimum_price' => 100000,
            'maximum_price' => 150000,
            'minimum_bedrooms' => 3,
            'minimum_land_size' => 1000,
            'page_size' => 15,
            'start_page' => 67,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $this->assertEquals('/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=66', $subject->previousPageUrl());
    }

    private function assertAssocArrayValuesContain($message, $array)
    {
        $result = array_filter($array, function($item) use ($message) {
            return array_filter(array_values($item), function ($value) use ($message) {
                if($message == $value) return true;
            });
        });

        $this->assertCount(1, $result, 'Value: "' . $message . '" was not found in array');
    }

}
