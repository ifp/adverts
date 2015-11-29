<?php
//[search_criteria] => Array
//(
//    [start_page] => 1
//                    [page_size] => 15
//                    [sort_by] => price
//                    [sort_direction] => asc
//                    [site] => Array
//(
//)
//
//[minimum_price] => 100000
//                    [maximum_price] => 500000
//                    [minimum_land_size] => 200
//                    [maximum_land_size] => 50000
//                    [minimum_bedrooms] => 1
//                    [maximum_bedrooms] => 6
//                    [keywords_en_any] => pool
//                    [title_en_any] => lake
//                    [regions] => Array
//(
//    [0] => aquitaine
//                            [1] => limousin
//                        )
//
//                    [departments] => Array
//(
//    [0] => dordogne
//                            [1] => cote-d'or
//                        )
//
//                )
//
class SearchSorterTest extends PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        return true;
    }

    public function estSorterCanCreateASortByPriceAscendingLinkForTheCurrentSearch()
    {
        $results = [
            "total" => 9997.7,
            "starting_from" => 991,
            "finishing_at" => 1005,
            "current_page" => 67,
            "total_pages" => 667
        ];

        $subject = new SearchSorter($search_criteria);

        $this->assertEquals(667, $subject->totalPages());

        $this->visit('/sale-advert-search?title_en_any=lake&minimum_price=100000')
            ->seeIsSelected('sort', '/sale-advert-search?title_en_any=lake&minimum_price=100000&sort_by=price&sort_direction=asc');
    }
}
