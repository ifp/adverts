<?php

use IFP\Adverts\Currency;
use IFP\Adverts\Sales\SearchExpander;

class SearchExpanderTest extends PHPUnit_Framework_TestCase
{
    private $example_currency_rates = [
        'EUR' => 1,
        'GBP' => 0.72,
        'USD' => 1.09,
        'CAD' => 1.49,
        'AUD' => 1.5,
        'CHF' => 1.08,
        'ZAR' => 16.93,
    ];

    // minimum price
    public function testTheMinimumPriceCanBeReturned()
    {
        $subject = new SearchExpander(null, ['minimum_price' => 100000], new Currency($this->example_currency_rates));

        $this->assertEquals(100000, $subject->minimumPrice());
    }

    public function testTheMinimumPriceReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->minimumPrice());
    }

    public function testTheMinimumPriceCriteriaIsDecreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(null, ['minimum_price' => 100000], new Currency($this->example_currency_rates));

        $this->assertEquals(80000, $subject->decreaseMinimumPriceByPercentage(20)->minimumPrice());
    }

    public function testTheMinimumPriceCriteriaIsDecreasedByASpecifiedPercentageAndRoundsDownToTheClosestInteger()
    {
        $subject = new SearchExpander(null, ['minimum_price' => 99997], new Currency($this->example_currency_rates));

        $this->assertEquals(79997, $subject->decreaseMinimumPriceByPercentage(20)->minimumPrice());
    }

    public function testTheMinimumPriceCriteriaIsIgnoredWhenTryingToDecreaseItByPercentageIfItWasNotSuppliedInTheSearchCriteria()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->decreaseMinimumPriceByPercentage(20)->minimumPrice());
    }

    public function testCheaperPropertiesCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'minimum_price' => 100000, 'minimum_bedrooms' => 7], new Currency($this->example_currency_rates));

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&minimum_price=75000&minimum_bedrooms=7',
            $subject->decreaseMinimumPriceByPercentage(25)->url()
        );
    }

    // maximum price
    public function testTheMaximumPriceCanBeReturned()
    {
        $subject = new SearchExpander(null, ['maximum_price' => 250000], new Currency($this->example_currency_rates));

        $this->assertEquals(250000, $subject->maximumPrice());
    }

    public function testTheMaximumPriceReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->maximumPrice());
    }

    public function testTheMaximumPriceCriteriaIsIncreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(null, ['maximum_price' => 100000], new Currency($this->example_currency_rates));

        $this->assertEquals(120000, $subject->increaseMaximumPriceByPercentage(20)->maximumPrice());
    }

    public function testTheMaximumPriceCriteriaIsIncreasedByASpecifiedPercentageAndRoundsUpToTheClosestInteger()
    {
        $subject = new SearchExpander(null, ['maximum_price' => 99997], new Currency($this->example_currency_rates));

        $this->assertEquals(119997, $subject->increaseMaximumPriceByPercentage(20)->maximumPrice());
    }

    public function testTheMaximumPriceCriteriaIsIgnoredWhenTryingToIncreaseItByPercentageIfItWasNotSuppliedInTheSearchCriteria()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->increaseMaximumPriceByPercentage(20)->maximumPrice());
    }

    public function testMoreExpensivePropertiesCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_price' => 250000, 'minimum_bedrooms' => 3], new Currency($this->example_currency_rates));

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_price=312500&minimum_bedrooms=3',
            $subject->increaseMaximumPriceByPercentage(25)->url()
        );
    }

    // minimum bedrooms
    public function testTheMinimumBedroomsCanBeReturned()
    {
        $subject = new SearchExpander(null, ['minimum_bedrooms' => 3], new Currency($this->example_currency_rates));

        $this->assertEquals(3, $subject->minimumBedrooms());
    }

    public function testTheMinimumBedroomsReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->minimumBedrooms());
    }

    public function testTheMinimumBedroomsCriteriaIsDecreasedByACertainNumber()
    {
        $subject = new SearchExpander(null, ['minimum_bedrooms' => 3], new Currency($this->example_currency_rates));

        $this->assertEquals(1, $subject->decreaseMinimumBedroomsByNumber(2)->minimumBedrooms());
    }

    public function testTheMinimumBedroomsCriteriaIsNotDecreasedIfTheMinimumNumberIsAlready0()
    {
        $subject = new SearchExpander(null, ['minimum_bedrooms' => 0], new Currency($this->example_currency_rates));

        $this->assertEquals(0, $subject->decreaseMinimumBedroomsByNumber(2)->minimumBedrooms());
    }

    public function testPropertiesWithLessBedroomsCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_price' => 250000, 'minimum_bedrooms' => 3], new Currency($this->example_currency_rates));

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_price=250000&minimum_bedrooms=2',
            $subject->decreaseMinimumBedroomsByNumber(1)->url()
        );
    }

    // max bedrooms
    public function testTheMaximumBedroomsCanBeReturned()
    {
        $subject = new SearchExpander(null, ['maximum_bedrooms' => 3], new Currency($this->example_currency_rates));

        $this->assertEquals(3, $subject->maximumBedrooms());
    }

    public function testTheMaximumBedroomsReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->maximumBedrooms());
    }

    public function testTheMaximumBedroomsCriteriaIsIncreasedByACertainNumber()
    {
        $subject = new SearchExpander(null, ['maximum_bedrooms' => 3], new Currency($this->example_currency_rates));

        $this->assertEquals(5, $subject->increaseMaximumBedroomsByNumber(2)->maximumBedrooms());
    }

    public function testPropertiesWithMoreBedroomsCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_price' => 250000, 'maximum_bedrooms' => 3], new Currency($this->example_currency_rates));

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_price=250000&maximum_bedrooms=4',
            $subject->increaseMaximumBedroomsByNumber(1)->url()
        );
    }

    // minimum land
    public function testTheMinimumLandSizeCanBeReturned()
    {
        $subject = new SearchExpander(null, ['minimum_land_size' => 100000], new Currency($this->example_currency_rates));

        $this->assertEquals(100000, $subject->minimumLandSize());
    }

    public function testTheMinimumLandSizeReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->minimumLandSize());
    }

    public function testTheMinimumLandSizeCriteriaIsDecreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(null, ['minimum_land_size' => 100000], new Currency($this->example_currency_rates));

        $this->assertEquals(80000, $subject->decreaseMinimumLandSizeByPercentage(20)->minimumLandSize());
    }

    public function testTheMinimumLandSizeCriteriaIsDecreasedByASpecifiedPercentageAndRoundsDownToTheClosestInteger()
    {
        $subject = new SearchExpander(null, ['minimum_land_size' => 99997], new Currency($this->example_currency_rates));

        $this->assertEquals(79997, $subject->decreaseMinimumLandSizeByPercentage(20)->minimumLandSize());
    }

    public function testTheMinimumLandSizeCriteriaIsIgnoredWhenTryingToDecreaseItByPercentageIfItWasNotSuppliedInTheSearchCriteria()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->decreaseMinimumLandSizeByPercentage(20)->minimumLandSize());
    }

    public function testPropertiesWithLessLandCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'minimum_land_size' => 100000, 'minimum_bedrooms' => 7], new Currency($this->example_currency_rates));

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&minimum_land_size=75000&minimum_bedrooms=7',
            $subject->decreaseMinimumLandSizeByPercentage(25)->url()
        );
    }

    // maximum land
    public function testTheMaximumLandSizeCanBeReturned()
    {
        $subject = new SearchExpander(null, ['maximum_land_size' => 250000], new Currency($this->example_currency_rates));

        $this->assertEquals(250000, $subject->maximumLandSize());
    }

    public function testTheMaximumLandSizeReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->maximumLandSize());
    }

    public function testTheMaximumLandSizeCriteriaIsIncreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(null, ['maximum_land_size' => 100000], new Currency($this->example_currency_rates));

        $this->assertEquals(120000, $subject->increaseMaximumLandSizeByPercentage(20)->maximumLandSize());
    }

    public function testTheMaximumLandSizeCriteriaIsIncreasedByASpecifiedPercentageAndRoundsUpToTheClosestInteger()
    {
        $subject = new SearchExpander(null, ['maximum_land_size' => 99997], new Currency($this->example_currency_rates));

        $this->assertEquals(119997, $subject->increaseMaximumLandSizeByPercentage(20)->maximumLandSize());
    }

    public function testTheMaximumLandSizeCriteriaIsIgnoredWhenTryingToIncreaseItByPercentageIfItWasNotSuppliedInTheSearchCriteria()
    {
        $subject = new SearchExpander(null, [], new Currency($this->example_currency_rates));

        $this->assertEquals(null, $subject->increaseMaximumLandSizeByPercentage(20)->maximumLandSize());
    }

    public function testPropertiesWithMoreLandCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_land_size' => 100000, 'minimum_bedrooms' => 7], new Currency($this->example_currency_rates));

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_land_size=125000&minimum_bedrooms=7',
            $subject->increaseMaximumLandSizeByPercentage(25)->url()
        );
    }

    public function testAnyKeywordsCanBeRemoved()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_land_size' => 100000, 'keywords_en_any' => 'lighthouse'], new Currency($this->example_currency_rates));

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_land_size=100000',
            $subject->removeAnyKeywords()->url()
        );
    }

    public function testAllKeywordsCanBeRemoved()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_land_size' => 100000, 'keywords_en_all' => 'lighthouse,pool'], new Currency($this->example_currency_rates));

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_land_size=100000',
            $subject->removeAllKeywords()->url()
        );
    }

    public function testSearchCriteriaCanBeResetToItsOriginalValues()
    {
        $subject = new SearchExpander('/sale-advert-search', ['minimum_price' => 100000, 'maximum_price' => 200000], new Currency($this->example_currency_rates));

        $subject->increaseMaximumPriceByPercentage(25);
        $subject->decreaseMinimumPriceByPercentage(25);

        $this->assertEquals(75000, $subject->minimumPrice());
        $this->assertEquals(250000, $subject->maximumPrice());

        $this->assertEquals(100000, $subject->reset()->minimumPrice());
        $this->assertEquals(200000, $subject->reset()->maximumPrice());
    }

    // Improved expansion tests

    private function assertStringDoesNotContain($string, $keyword, $message = "")
    {
        $contains_string = preg_match("/$keyword/", $string);

        if($contains_string) {
            if($message == "") {
                $this->fail("String '$string' contains '$keyword' (IFP custom assertion)");
            }

            $this->fail("String '$string' contains '$keyword' (IFP custom assertion):\n - $message");
        }
    }

    // Price criteria only option
    public function testComplicatedCriteriaOffersAnOptionForExpandedPriceOnlyOptionWithMinAndMaxPriceSpecified()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "title_en_any" => 'lake',
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 20000,
            "maximum_price" => 40000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&minimum_price=15000&maximum_price=50000', $offered_options);
    }

    public function testComplicatedCriteriaOffersAnOptionForExpandedPriceOnlyOptionWithOnlyMaxPriceSpecified()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "title_en_any" => 'lake',
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "maximum_price" => 40000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&maximum_price=50000', $offered_options);
    }

    public function testComplicatedCriteriaOffersAnOptionForExpandedPriceOnlyOptionWithOnlyMinPriceSpecified()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "title_en_any" => 'lake',
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 20000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&minimum_price=15000', $offered_options);
    }

    public function testComplicatedCriteriaWithoutBudgetSpecifiedDoesNotOfferAnyPriceOptions()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "title_en_any" => 'lake',
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'minimum_price', 'budget criteria expansion option was given when the user did not specify a budget criteria');
            $this->assertStringDoesNotContain($offered_option_url, 'maximum_price', 'budget criteria expansion option was given when the user did not specify a budget criteria');
        }
    }

    // Bedroom criteria only option
    public function testComplicatedCriteriaOffersAnOptionForExpandedBedroomOnlyOptionWithMinAndMaxBedroomsSpecified()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "title_en_any" => 'lake',
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 20000,
            "maximum_price" => 40000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?minimum_bedrooms=0&maximum_bedrooms=3', $offered_options);
    }

//    public function testComplicatedCriteriaOffersAnOptionForExpandedPriceOnlyOptionWithOnlyMaxPriceSpecified()
//    {
//        $subject = new SearchExpander('/sale-advert-search', [
//            "title_en_any" => 'lake',
//            "keywords_en_any" => "qwertykeyword",
//            "currency" => "EUR",
//            "maximum_price" => 40000,
//            "minimum_bedrooms" => "1",
//            "maximum_bedrooms" => "2",
//            "land_size_unit" => "Hectares",
//            "minimum_land_size" => 1.0,
//            "maximum_land_size" => 3.0,
//        ], new Currency($this->example_currency_rates));
//
//        $offered_options = $subject->getExpansionOptions();
//
//        $this->assertContains('/sale-advert-search?currency=EUR&maximum_price=50000', $offered_options);
//    }
//
//    public function testComplicatedCriteriaOffersAnOptionForExpandedPriceOnlyOptionWithOnlyMinPriceSpecified()
//    {
//        $subject = new SearchExpander('/sale-advert-search', [
//            "title_en_any" => 'lake',
//            "keywords_en_any" => "qwertykeyword",
//            "currency" => "EUR",
//            "minimum_price" => 20000,
//            "minimum_bedrooms" => "1",
//            "maximum_bedrooms" => "2",
//            "land_size_unit" => "Hectares",
//            "minimum_land_size" => 1.0,
//            "maximum_land_size" => 3.0,
//        ], new Currency($this->example_currency_rates));
//
//        $offered_options = $subject->getExpansionOptions();
//
//        $this->assertContains('/sale-advert-search?currency=EUR&minimum_price=15000', $offered_options);
//    }
//
//    public function testComplicatedCriteriaWithoutBudgetSpecifiedDoesNotOfferAnyPriceOptions()
//    {
//        $subject = new SearchExpander('/sale-advert-search', [
//            "title_en_any" => 'lake',
//            "keywords_en_any" => "qwertykeyword",
//            "currency" => "EUR",
//            "minimum_bedrooms" => "1",
//            "maximum_bedrooms" => "2",
//            "land_size_unit" => "Hectares",
//            "minimum_land_size" => 1.0,
//            "maximum_land_size" => 3.0,
//        ], new Currency($this->example_currency_rates));
//
//        $offered_options = $subject->getExpansionOptions();
//
//        foreach($offered_options as $offered_option_url) {
//            $this->assertStringDoesNotContain($offered_option_url, 'minimum_price', 'budget criteria expansion option was given when the user did not specify a budget criteria');
//            $this->assertStringDoesNotContain($offered_option_url, 'maximum_price', 'budget criteria expansion option was given when the user did not specify a budget criteria');
//        }
//    }
}
