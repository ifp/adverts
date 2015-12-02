<?php

use IFP\Adverts\Sales\SearchSorter;

class SearchSorterTest extends PHPUnit_Framework_TestCase
{
    // The SearchSorter makes links which start from the first page of the sorted results,
    // thus we drop the pagination criteria: start_page and page_size

    public function testSorterCanCreateASortByPriceAscendingUrlForTheCurrentSearch()
    {
        $search_criteria = [
            "title_en_any" => "lake",
            "start_page" => 1,
            "page_size" => 15,
            "minimum_price" => 100000,
            "maximum_price" => 2000000,
            "minimum_bedrooms" => 1,
            "maximum_bedrooms" => 6,
            "minimum_land_size" => 300,
            "maximum_land_size" => 400000,
            "sort_by" => "price",
            "sort_direction" => "asc",
            "regions" => [
                "aquitaine",
                "centre",
            ],
            "departments" => [
                "dordogne",
                "haute-loire",
            ],
            "keywords_en_any" => "swimming pool",
        ];

        $base_url = '/sale-advert-search';

        $subject = new SearchSorter($base_url, $search_criteria);

        $this->assertEquals('/sale-advert-search?title_en_any=lake&minimum_price=100000&maximum_price=2000000&minimum_bedrooms=1&maximum_bedrooms=6&minimum_land_size=300&maximum_land_size=400000&regions=aquitaine,centre&departments=dordogne,haute-loire&keywords_en_any=swimming+pool&sort_by=price&sort_direction=asc', $subject->priceAscendingUrl());
    }

    public function testSorterCanCreateASortByPriceDescendingUrlForTheCurrentSearch()
    {
        $search_criteria = [
            "title_en_any" => "lake",
            "start_page" => 1,
            "page_size" => 15,
            "minimum_price" => 100000,
            "maximum_price" => 2000000,
            "minimum_bedrooms" => 1,
            "maximum_bedrooms" => 6,
            "minimum_land_size" => 300,
            "maximum_land_size" => 400000,
            "sort_by" => "price",
            "sort_direction" => "asc",
            "regions" => [
                "aquitaine",
                "centre",
            ],
            "departments" => [
                "dordogne",
                "haute-loire",
            ],
            "keywords_en_any" => "swimming pool",
        ];

        $base_url = '/sale-advert-search';

        $subject = new SearchSorter($base_url, $search_criteria);

        $this->assertEquals('/sale-advert-search?title_en_any=lake&minimum_price=100000&maximum_price=2000000&minimum_bedrooms=1&maximum_bedrooms=6&minimum_land_size=300&maximum_land_size=400000&regions=aquitaine,centre&departments=dordogne,haute-loire&keywords_en_any=swimming+pool&sort_by=price&sort_direction=desc', $subject->priceDescendingUrl());
    }

    public function testSorterCanCreateASortByDateDescendingUrlForTheCurrentSearch()
    {
        $search_criteria = [
            "title_en_any" => "lake",
            "start_page" => 1,
            "page_size" => 15,
            "minimum_price" => 100000,
            "maximum_price" => 2000000,
            "minimum_bedrooms" => 1,
            "maximum_bedrooms" => 6,
            "minimum_land_size" => 300,
            "maximum_land_size" => 400000,
            "sort_by" => "price",
            "sort_direction" => "asc",
            "regions" => [
                "aquitaine",
                "centre",
            ],
            "departments" => [
                "dordogne",
                "haute-loire",
            ],
            "keywords_en_any" => "swimming pool",
        ];

        $base_url = '/sale-advert-search';

        $subject = new SearchSorter($base_url, $search_criteria);

        $this->assertEquals('/sale-advert-search?title_en_any=lake&minimum_price=100000&maximum_price=2000000&minimum_bedrooms=1&maximum_bedrooms=6&minimum_land_size=300&maximum_land_size=400000&regions=aquitaine,centre&departments=dordogne,haute-loire&keywords_en_any=swimming+pool&sort_by=date&sort_direction=desc', $subject->dateDescendingUrl());
    }

    public function testSorterCanIdentifyPriceAscendingSortIsCurrentlySelected()
    {
        $search_criteria = [
            "title_en_any" => "lake",
            "start_page" => 1,
            "page_size" => 15,
            "minimum_price" => 100000,
            "maximum_price" => 2000000,
            "minimum_bedrooms" => 1,
            "maximum_bedrooms" => 6,
            "minimum_land_size" => 300,
            "maximum_land_size" => 400000,
            "sort_by" => "price",
            "sort_direction" => "asc",
            "regions" => [
                "aquitaine",
                "centre",
            ],
            "departments" => [
                "dordogne",
                "haute-loire",
            ],
            "keywords_en_any" => "swimming pool",
        ];

        $base_url = '/sale-advert-search';

        $subject = new SearchSorter($base_url, $search_criteria);

        $this->assertEquals('price_asc', $subject->currentSort());
    }

    public function testSorterCanIdentifyPriceDescendingSortIsCurrentlySelected()
    {
        $search_criteria = [
            "title_en_any" => "lake",
            "start_page" => 1,
            "page_size" => 15,
            "minimum_price" => 100000,
            "maximum_price" => 2000000,
            "minimum_bedrooms" => 1,
            "maximum_bedrooms" => 6,
            "minimum_land_size" => 300,
            "maximum_land_size" => 400000,
            "sort_by" => "price",
            "sort_direction" => "desc",
            "regions" => [
                "aquitaine",
                "centre",
            ],
            "departments" => [
                "dordogne",
                "haute-loire",
            ],
            "keywords_en_any" => "swimming pool",
        ];

        $base_url = '/sale-advert-search';

        $subject = new SearchSorter($base_url, $search_criteria);

        $this->assertEquals('price_desc', $subject->currentSort());
    }

    public function testSorterCanIdentifyDateDescendingSortIsCurrentlySelected()
    {
        $search_criteria = [
            "title_en_any" => "lake",
            "start_page" => 1,
            "page_size" => 15,
            "minimum_price" => 100000,
            "maximum_price" => 2000000,
            "minimum_bedrooms" => 1,
            "maximum_bedrooms" => 6,
            "minimum_land_size" => 300,
            "maximum_land_size" => 400000,
            "sort_by" => "date",
            "sort_direction" => "desc",
            "regions" => [
                "aquitaine",
                "centre",
            ],
            "departments" => [
                "dordogne",
                "haute-loire",
            ],
            "keywords_en_any" => "swimming pool",
        ];

        $base_url = '/sale-advert-search';

        $subject = new SearchSorter($base_url, $search_criteria);

        $this->assertEquals('date_desc', $subject->currentSort());
    }
}
