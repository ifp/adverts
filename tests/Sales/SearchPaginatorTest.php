<?php

use IFP\Adverts\InvalidPaginationDataException;
use IFP\Adverts\Sales\SearchPaginator;

class SearchPaginatorTest extends PHPUnit_Framework_TestCase
{
    public function testItThrowsWhenGivenNoResultsData()
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

    public function testItThrowsWhenGivenInvalidResultsData()
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

    public function testItCanSpecifyTotalNumberOfResultsGivenValidResultsData()
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

    public function testItCanSpecifyStartingFromNumberOfPageGivenValidResultsData()
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

    public function testItCanSpecifyFinishingAtNumberOfPageGivenValidResultsData()
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

    public function testItCanSpecifyCurrentPageNumberGivenValidResultsData()
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

    public function testItCanSpecifyTotalNumberOfPagesGivenValidResultsData()
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

    public function testItProvidesZeroesWhenGivenZeroedResultsData()
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

    public function testItCanTellThatItDoesNotHaveMorePagesOfResults()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 23,
            "total_pages" => 23
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertFalse($subject->hasMorePages());
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

    public function testItCanTellThatItDoesNotHaveLessPagesOfResults()
    {
        $results = [
            "total" => 0,
            "starting_from" => 0,
            "finishing_at" => 0,
            "current_page" => 0,
            "total_pages" => 0,
        ];

        $subject = new SearchPaginator('', [], $results);

        $this->assertFalse($subject->hasLessPages());
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

        $this->assertEquals(false, $subject->nextPage());
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

        $this->assertEquals(false, $subject->previousPage());
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

    public function testItProvidesCurrentPageUrlWhenGivenValidData()
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

        $this->assertEquals('/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=67', $subject->currentPageUrl());
    }

    public function testItProvidesFirstPageUrlWhenGivenValidData()
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

    public function testItProvidesLastPageUrlWhenGivenValidData()
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

    public function testItProvidesNextPageUrlWhenGivenValidData()
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

    public function testItDoesNotProvideNextPageUrlWhenNextPageDoesNotExist()
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
            'start_page' => 667,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 667,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $this->assertEquals(false, $subject->nextPageUrl());
    }

    public function testItProvidesPreviousPageUrlWhenGivenValidData()
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

    public function testItDoesNotProvidePreviousPageUrlWhenPreviousPageDoesNotExist()
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
            "current_page" => 1,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $this->assertEquals(false, $subject->previousPageUrl());
    }

    public function testItProvidesNextFivePageUrlsAndNextAndLastPageUrlsWhenGivenValidData()
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

        $assertion = [
            [
                'page_number' => 68,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=68'
            ],
            [
                'page_number' => 69,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=69'
            ],
            [
                'page_number' => 70,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=70'
            ],
            [
                'page_number' => 71,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=71'
            ],
            [
                'page_number' => 72,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=72'
            ],
            [
                'page_number' => 68,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=68'
            ],
            [
                'page_number' => 667,
                'page_type' => 'last',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=667'
            ],
        ];

        $this->assertEquals($assertion, $subject->nextPagesUrls(5));
    }

    public function testItProvidesPreviousThreePageUrlsAndFirstPageUrlsWhenGivenValidData()
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
            "current_page" => 9,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 1,
                'page_type' => 'first',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 8,
                'page_type' => 'previous',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=8'
            ],
            [
                'page_number' => 6,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=6'
            ],
            [
                'page_number' => 7,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=7'
            ],
            [
                'page_number' => 8,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=8'
            ],
        ];

        $this->assertEquals($assertion, $subject->previousPagesUrls(3));
    }

    public function testItProvidesNextPageUrlsToLastPageWhenMoreThenRemainAreRequested()
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
            'start_page' => 665,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 665,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 666,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=666'
            ],
            [
                'page_number' => 667,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=667'
            ],
            [
                'page_number' => 666,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=666'
            ],
        ];

        $this->assertEquals($assertion, $subject->nextPagesUrls(5));
    }

    public function testItProvidesPreviousPageUrlsToFirstPageWhenMoreThenExistAreRequested()
    {
        $base_url = 'http://localhost/sale-advert-search';

        $search_criteria = [
            'title_en_any' => 'house with garden',
            'keywords_en_any' => 'swimming pool',
            'minimum_price' => 100000,
            'maximum_price' => 150000,
            'minimum_bedrooms' => 3,
            'minimum_land_size' => 1000,
            'page_size' => 15,
            'start_page' => 5,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 5,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 4,
                'page_type' => 'previous',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=4'
            ],
            [
                'page_number' => 1,
                'page_type' => 'preceding',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 2,
                'page_type' => 'preceding',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=2'
            ],
            [
                'page_number' => 3,
                'page_type' => 'preceding',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=3'
            ],
            [
                'page_number' => 4,
                'page_type' => 'preceding',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=4'
            ],
        ];

        $this->assertEquals($assertion, $subject->previousPagesUrls(10));
    }

    public function testItGeneratesUrlsInBothDirectionsFromAPageInTheMiddle()
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
            'start_page' => 655,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 655,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 1,
                'page_type' => 'first',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 654,
                'page_type' => 'previous',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=654'
            ],
            [
                'page_number' => 650,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=650'
            ],
            [
                'page_number' => 651,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=651'
            ],
            [
                'page_number' => 652,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=652'
            ],
            [
                'page_number' => 653,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=653'
            ],
            [
                'page_number' => 654,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=654'
            ],
            [
                'page_number' => 655,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=655'
            ],
            [
                'page_number' => 656,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=656'
            ],
            [
                'page_number' => 657,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=657'
            ],
            [
                'page_number' => 658,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=658'
            ],
            [
                'page_number' => 659,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=659'
            ],
            [
                'page_number' => 660,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=660'
            ],
            [
                'page_number' => 656,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=656'
            ],
            [
                'page_number' => 667,
                'page_type' => 'last',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=667'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(10));
    }

    public function testItGenerates10UrlsInBothDirectionsFrom3rdPageInTheResults()
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
            'start_page' => 3,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 3,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 2,
                'page_type' => 'previous',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=2'
            ],
            [
                'page_number' => 1,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 2,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=2'
            ],
            [
                'page_number' => 3,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=3'
            ],
            [
                'page_number' => 4,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=4'
            ],
            [
                'page_number' => 5,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=5'
            ],
            [
                'page_number' => 6,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=6'
            ],
            [
                'page_number' => 7,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=7'
            ],
            [
                'page_number' => 8,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=8'
            ],
            [
                'page_number' => 9,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=9'
            ],
            [
                'page_number' => 10,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=10'
            ],
            [
                'page_number' => 11,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=11'
            ],
            [
                'page_number' => 4,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=4'
            ],
            [
                'page_number' => 667,
                'page_type' => 'last',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=667'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(10));
    }

    public function testItGenerates10UrlsInBothDirectionsFrom3rdToLastPageInTheResults()
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
            'start_page' => 665,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 665,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 1,
                'page_type' => 'first',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 664,
                'page_type' => 'previous',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=664'
            ],
            [
                'page_number' => 657,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=657'
            ],
            [
                'page_number' => 658,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=658'
            ],
            [
                'page_number' => 659,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=659'
            ],
            [
                'page_number' => 660,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=660'
            ],
            [
                'page_number' => 661,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=661'
            ],
            [
                'page_number' => 662,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=662'
            ],
            [
                'page_number' => 663,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=663'
            ],
            [
                'page_number' => 664,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=664'
            ],
            [
                'page_number' => 665,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=665'
            ],
            [
                'page_number' => 666,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=666'
            ],
            [
                'page_number' => 667,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=667'
            ],
            [
                'page_number' => 666,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=666'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(10));
    }

    public function testItGeneratesUrlsInBothDirectionsFromCurrentPageWhenMorePagesAreRequestedThanAvailable()
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
            'start_page' => 4,
        ];

        $results = [
            "total" => 90,
            "starting_from" => 46,
            "finishing_at" => 60,
            "current_page" => 4,
            "total_pages" => 6
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 3,
                'page_type' => 'previous',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=3'
            ],
            [
                'page_number' => 1,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 2,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=2'
            ],
            [
                'page_number' => 3,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=3'
            ],
            [
                'page_number' => 4,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=4'
            ],
            [
                'page_number' => 5,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=5'
            ],
            [
                'page_number' => 6,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=6'
            ],
            [
                'page_number' => 5,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=5'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(10));
    }

    public function testItGeneratesUpcomingUrlsAndNextUrlAndLastUrlOnlyWhenCurrentPageIsFirstPage()
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
            'start_page' => 1,
        ];

        $results = [
            "total" => 90,
            "starting_from" => 1,
            "finishing_at" => 15,
            "current_page" => 1,
            "total_pages" => 60
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 1,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 2,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=2'
            ],
            [
                'page_number' => 3,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=3'
            ],
            [
                'page_number' => 4,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=4'
            ],
            [
                'page_number' => 5,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=5'
            ],
            [
                'page_number' => 2,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=2'
            ],
            [
                'page_number' => 60,
                'page_type' => 'last',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=60'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(4));
    }

    public function testItGeneratesCorrectUrlsWhenCurrentPageIsSecondPage()
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
            'start_page' => 1,
        ];

        $results = [
            "total" => 90,
            "starting_from" => 1,
            "finishing_at" => 15,
            "current_page" => 2,
            "total_pages" => 60
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 1,
                'page_type' => 'previous',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 1,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 2,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=2'
            ],
            [
                'page_number' => 3,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=3'
            ],
            [
                'page_number' => 4,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=4'
            ],
            [
                'page_number' => 5,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=5'
            ],
            [
                'page_number' => 3,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=3'
            ],
            [
                'page_number' => 60,
                'page_type' => 'last',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=60'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(4));
    }

    public function testItGeneratesCorrectUrlsWhenCurrentPageIsLastPage()
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
            'start_page' => 6,
        ];

        $results = [
            "total" => 90,
            "starting_from" => 1,
            "finishing_at" => 15,
            "current_page" => 60,
            "total_pages" => 60
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 1,
                'page_type' => 'first',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 59,
                'page_type' => 'previous',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=59'
            ],
            [
                'page_number' => 56,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=56'
            ],
            [
                'page_number' => 57,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=57'
            ],
            [
                'page_number' => 58,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=58'
            ],
            [
                'page_number' => 59,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=59'
            ],
            [
                'page_number' => 60,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=60'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(4));
    }

    public function testItGeneratesCorrectUrlsWhenCurrentPageIsSecondToLastPage()
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
            'start_page' => 6,
        ];

        $results = [
            "total" => 90,
            "starting_from" => 1,
            "finishing_at" => 15,
            "current_page" => 59,
            "total_pages" => 60
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 1,
                'page_type' => 'first',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
            [
                'page_number' => 58,
                'page_type' => 'previous',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=58'
            ],
            [
                'page_number' => 56,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=56'
            ],
            [
                'page_number' => 57,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=57'
            ],
            [
                'page_number' => 58,
                'page_type' => 'preceding',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=58'
            ],
            [
                'page_number' => 59,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=59'
            ],
            [
                'page_number' => 60,
                'page_type' => 'upcoming',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=60'
            ],
            [
                'page_number' => 60,
                'page_type' => 'next',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=60'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(4));
    }

    public function testItGeneratesASingleURLWhenCurrentPageIsTheOnlyPage()
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
            'start_page' => 1,
        ];

        $results = [
            "total" => 15,
            "starting_from" => 1,
            "finishing_at" => 15,
            "current_page" => 1,
            "total_pages" => 1,
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 1,
                'page_type' => 'current',
                'url' => '/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&page_size=15&start_page=1'
            ],
        ];

        $this->assertEquals($assertion, $subject->scrollPagesUrls(10));
    }

    public function testItGeneratesArrayQueryStringVariablesCorrectly()
    {
        $base_url = 'http://localhost/sale-advert-search';

        $search_criteria = [
            'title_en_any' => 'house with garden',
            'keywords_en_any' => 'swimming pool',
            'minimum_price' => 100000,
            'maximum_price' => 150000,
            'minimum_bedrooms' => 3,
            'minimum_land_size' => 1000,
            'geo' => [
                'lat' => 47.123,
                'lon' => 1.2345,
                'distance' => '50km',
            ],
            'page_size' => 15,
            'start_page' => 3,
        ];

        $results = [
            "total" => 9997,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 5,
            "total_pages" => 667
        ];

        $subject = new SearchPaginator($base_url, $search_criteria, $results);

        $assertion = [
            [
                'page_number' => 4,
                'page_type' => 'previous',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&geo[lat]=47.123&geo[lon]=1.2345&geo[distance]=50km&page_size=15&start_page=4'
            ],
            [
                'page_number' => 1,
                'page_type' => 'preceding',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&geo[lat]=47.123&geo[lon]=1.2345&geo[distance]=50km&page_size=15&start_page=1'
            ],
            [
                'page_number' => 2,
                'page_type' => 'preceding',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&geo[lat]=47.123&geo[lon]=1.2345&geo[distance]=50km&page_size=15&start_page=2'
            ],
            [
                'page_number' => 3,
                'page_type' => 'preceding',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&geo[lat]=47.123&geo[lon]=1.2345&geo[distance]=50km&page_size=15&start_page=3'
            ],
            [
                'page_number' => 4,
                'page_type' => 'preceding',
                'url' => 'http://localhost/sale-advert-search?title_en_any=house+with+garden&keywords_en_any=swimming+pool&minimum_price=100000&maximum_price=150000&minimum_bedrooms=3&minimum_land_size=1000&geo[lat]=47.123&geo[lon]=1.2345&geo[distance]=50km&page_size=15&start_page=4'
            ],
        ];

        $outcome = $subject->previousPagesUrls(10);

        $this->assertCount(5, $outcome);
        $this->assertEquals($assertion[0]['page_number'], $outcome[0]['page_number']);
        $this->assertUrlEquals($assertion[0]['url'], $outcome[0]['url']);
        $this->assertEquals($assertion[1]['page_number'], $outcome[1]['page_number']);
        $this->assertUrlEquals($assertion[1]['url'], $outcome[1]['url']);
        $this->assertEquals($assertion[2]['page_number'], $outcome[2]['page_number']);
        $this->assertUrlEquals($assertion[2]['url'], $outcome[2]['url']);
        $this->assertEquals($assertion[3]['page_number'], $outcome[3]['page_number']);
        $this->assertUrlEquals($assertion[3]['url'], $outcome[3]['url']);
        $this->assertEquals($assertion[4]['page_number'], $outcome[4]['page_number']);
        $this->assertUrlEquals($assertion[4]['url'], $outcome[4]['url']);
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

    public function assertUrlEquals($expected, $result)
    {
        $expected = parse_url($expected);
        $result= parse_url($result);
        $this->assertEquals($expected['scheme'], $result['scheme']);
        $this->assertEquals($expected['host'], $result['host']);
        $this->assertEquals($expected['path'], $result['path']);

        parse_str($expected['query'], $expected_query);
        parse_str($result['query'], $result_query);
        $this->assertEquals($expected_query, $result_query);
    }
}
