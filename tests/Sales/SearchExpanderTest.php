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

    //
    // New (still crude) expansion system (2016-07-28)
    //

    private function assertStringDoesNotContain($string, $keyword, $message = "")
    {
        $contains_string = preg_match("/$keyword/", $string);

        if($contains_string) {
            if($message == "") {
                $this->fail("String '$string' contains '$keyword' (IFP custom assertion)");
            }

            $this->fail("String '$string' contains '$keyword' (IFP custom assertion):\n - $message");
        }

        $this->assertEquals(true, true); //Just to add to the assertion counter
    }
    private function assertStringContains($string, $keyword, $message = "")
    {
        $contains_string = preg_match("/$keyword/", $string);

        if(!$contains_string) {
            if($message == "") {
                $this->fail("String '$string' does not contain '$keyword' (IFP custom assertion)");
            }

            $this->fail("String '$string' does not contain '$keyword' (IFP custom assertion):\n - $message");
        }

        $this->assertEquals(true, true); //Just to add to the assertion counter
    }

    // Temporary - ensure title_en_any keywords are passed through (ensure Lakes France only shows lakes until advert checker is in use)
    public function testTitleEnAnyAlwaysPassedThroughForTheMomentWithMultipleCriteriaSets()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "title_en_any" => "foobarproperties",
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringContains($offered_option_url, "title_en_any=foobarproperties",
                'title_en_any criteria stripped off which network sites currently rely on for filtering - temporary');
        }
    }
    // Temporary - ensure title_en_any keywords are passed through (ensure Lakes France only shows lakes until advert checker is in use)
    public function testTitleEnAnyAlwaysPassedThroughForTheMomentWithSingleCriteria()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "title_en_any" => "foobarproperties",
            "currency" => "EUR",
            "maximum_price" => 60000,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringContains($offered_option_url, "title_en_any=foobarproperties",
                'title_en_any criteria stripped off which network sites currently rely on for filtering - temporary');
        }
    }

    //
    // Complex (multiple sets specified e.g. bedroom and budget) criteria - always offer to reduce down to just one criteria set
    //

    // Multiple criteria - Budgets
    public function testMultipleCriteriaSetsOffersAnOptionForExpandedBudgetOnlyOptionWithBudgetSpecified()
    {
        //////// Min & Max price:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&minimum_price=30000&maximum_price=75000', $offered_options);

        //////// Maximum price only:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&maximum_price=75000', $offered_options);

        //////// Minimum price only:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&minimum_price=30000', $offered_options);
    }

    //Andy thinks: Is this test necessary?
    public function testMultipleCriteriaSetsWithoutBudgetSpecifiedDoesNotOfferAnyPriceOptions()
    {
        $subject = new SearchExpander('/sale-advert-search', [
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

    public function testMultipleCriteriaSetsWithVeryLowMaxBudgetSpecifiedDoesNotOfferRaisedMaxBudget()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 10000,
            "maximum_price" => 20000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'maximum_price', 'original specified budget criteria too low - should not attempt percentage increase');
        }

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "maximum_price" => 20000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'maximum_price', 'original specified max budget criteria too low - should not attempt percentage increase');
        }
    }

    public function testMultipleCriteriaSetsWithVeryHighMinBudgetSpecifiedDoesNotOfferLoweredMinBudget()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 1000000,
            "maximum_price" => 2000000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'minimum_price', 'original specified min budget criteria too high - should not attempt percentage decrease');
        }

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 1000000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'maximum_price', 'original specified budget criteria too low - should not attempt percentage increase');
        }
    }

    public function testMultipleCriteriaSetsWithVeryHighMinOrVeryLowMaxBudgetSpecifiedInUsdDoesNotOfferAdjustedBudget()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "USD",
            "minimum_price" => 1000000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'minimum_price', 'original specified minimum budget criteria too high - should not attempt percentage decrease');
        }

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "USD",
            "minimum_price" => 52000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'maximum_price', 'original specified max budget criteria too low - should not attempt percentage increase');
        }
    }

    public function testPricesRoundedWhenOfferingExpandedPriceOnlyOption()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40078,
            "maximum_price" => 60027,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&minimum_price=30000&maximum_price=75000', $offered_options);
    }

    // Multiple criteria - Bedrooms
    public function testMultipleCriteriaSetsWithBedroomsOffersABedroomOnlyOption()
    {
        //////// Min and max bedrooms

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&minimum_bedrooms=1&maximum_bedrooms=2', $offered_options);

        //////// Min bedrooms only

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&minimum_bedrooms=1', $offered_options);

        //////// Max bedrooms only

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "maximum_bedrooms" => "1",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&maximum_bedrooms=1', $offered_options);
    }

    // Multiple criteria - Land
    public function testMultipleCriteriaSetsOffersALandOnlyOptionWhenLandSpecified()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&land_size_unit=Hectares&minimum_land_size=0&maximum_land_size=4', $offered_options);
    }

    public function testMultipleCriteriaSetsDoesNotOffersALandOnlyOptionWhenMinimumLandVeryHigh()
    {
        //////// Min and max land specified:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 200000.0,
            "maximum_land_size" => 100000.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach ($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'minimum_land_size');
        }

        //////// Min only specified:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 200000.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach ($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'minimum_land_size');
        }
    }

    public function testMultipleCriteriaSetsOffersALandOnlyOptionWhenMinimumLandInMetresSquared()
    {
        //////// Min and max specified in m²:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "m²",
            "minimum_land_size" => 190000.0, //19 hectares - below limit
            "maximum_land_size" => 250000.0, //25 hectares
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&land_size_unit=m%C2%B2&minimum_land_size=142500&maximum_land_size=312500', $offered_options);

        //////// Min only, in m²:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "m²",
            "minimum_land_size" => 190000.0, //19 hectares - below limit
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&land_size_unit=m%C2%B2&minimum_land_size=142500', $offered_options);

        //////// Min only, in m², too high:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "m²",
            "minimum_land_size" => 800000.0, //80 hectares - above limit - should not offer an expanded option as the minimum is too high
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach ($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'minimum_land_size');
        }
    }

    public function testMultipleCriteriaSetsOffersALandOnlyOptionWhenMinimumLandInAcres()
    {
        //////// Min and max specified in acres:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Acres",
            "minimum_land_size" => 100, //45 hectares - below limit - should offer to expand this
            "maximum_land_size" => 150,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&land_size_unit=Acres&minimum_land_size=75&maximum_land_size=188', $offered_options);

        //////// Min only, in m²:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Acres",
            "minimum_land_size" => 100, //45 hectares - below limit - should offer to expand this
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&land_size_unit=Acres&minimum_land_size=75', $offered_options);

        //////// Min only, in acres, too high:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Acres",
            "minimum_land_size" => 200, //80 hectares - above limit - should not offer an expanded option as the minimum is too high
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        foreach ($offered_options as $offered_option_url) {
            $this->assertStringDoesNotContain($offered_option_url, 'minimum_land_size');
        }
    }

    // Multiple criteria - Locations
    public function testMultipleCriteriaSetsOffersALocationOnlyOptionWhenRegionsAndDepartmentsSpecified()
    {
        //////// Regions & Departments:
        
        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
            "regions" => ["alsace"],
            "departments" => ["landes"],
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&regions=alsace&departments=landes', $offered_options);

        //////// Regions only:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
            "regions" => ["alsace"],
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&regions=alsace', $offered_options);

        //////// Departments only:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
            "departments" => ["landes"],
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&departments=landes', $offered_options);
    }

    // Multiple criteria - Keywords
    public function testMultipleCriteriaSetsOfferAKeywordOnlyOptionWhenKeywordsSpecified()
    {
        //////// One keyword:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
            "regions" => ["alsace"],
            "departments" => ["landes"],
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&regions=alsace&departments=landes', $offered_options);

        //////// Many keywords:

        $subject = new SearchExpander('/sale-advert-search', [
            "keywords_en_any" => "qwertykeyword,abckeyword",
            "currency" => "EUR",
            "minimum_price" => 40000,
            "maximum_price" => 60000,
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
            "regions" => ["alsace"],
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR&regions=alsace', $offered_options);
    }

    //
    // Single criteria - always offer to remove
    //
    public function testSingleCriteriaWithBudgetOnlyAlwaysOffersToRemoveYourBudgetCriteria()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "maximum_price" => 5000,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "minimum_price" => 5000000
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "maximum_price" => 5000,
            "minimum_price" => 5000000
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);
    }

    public function testSingleCriteriaWithBedroomOnlyAlwaysOffersToRemoveYourBedroomCriteria()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "maximum_bedrooms" => "2",
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "minimum_bedrooms" => "1",
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "minimum_bedrooms" => "1",
            "maximum_bedrooms" => "2",
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);
    }

    public function testSingleCriteriaWithLandOnlyAlwaysOffersToRemoveYourLandCriteria()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "land_size_unit" => "Hectares",
            "maximum_land_size" => 1.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "land_size_unit" => "Hectares",
            "minimum_land_size" => 1.0,
            "maximum_land_size" => 3.0,
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);
    }

    public function testSingleCriteriaWithLocationOnlyAlwaysOffersToRemoveYourLocationCriteria()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "regions" => ["alsace"],
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "departments" => ["landes"],
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "regions" => ["alsace"],
            "departments" => ["landes"],
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);
    }

    public function testSingleCriteriaWithKeywordOnlyAlwaysOffersToRemoveYourKeywordCriteria()
    {
        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "keywords_en_any" => "qwertykeyword",
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);

        ////////

        $subject = new SearchExpander('/sale-advert-search', [
            "currency" => "EUR",
            "keywords_en_any" => "qwertykeyword,abckeyword",
        ], new Currency($this->example_currency_rates));

        $offered_options = $subject->getExpansionOptions();

        $this->assertContains('/sale-advert-search?currency=EUR', $offered_options);
    }
}
