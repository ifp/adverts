<?php

use IFP\Adverts\Sales\SearchExpander;

class SearchExpanderTest extends PHPUnit_Framework_TestCase
{
    public function testTheMinimumPriceCanBeReturned()
    {
        $subject = new SearchExpander(null, ['minimum_price' => 100000]);

        $this->assertEquals(100000, $subject->minimumPrice());
    }

    public function testTheMinimumPriceReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->minimumPrice());
    }

    public function testTheMinimumPriceCriteriaIsDecreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(null, ['minimum_price' => 100000]);

        $this->assertEquals(80000, $subject->decreaseMinimumPriceByPercentage(20)->minimumPrice());
    }

    public function testTheMinimumPriceCriteriaIsDecreasedByASpecifiedPercentageAndRoundsDownToTheClosestInteger()
    {
        $subject = new SearchExpander(null, ['minimum_price' => 99997]);

        $this->assertEquals(79997, $subject->decreaseMinimumPriceByPercentage(20)->minimumPrice());
    }

    public function testTheMinimumPriceCriteriaIsIgnoredWhenTryingToDecreaseItByPercentageIfItWasNotSuppliedInTheSearchCriteria()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->decreaseMinimumPriceByPercentage(20)->minimumPrice());
    }

    public function testCheaperPropertiesCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'minimum_price' => 100000, 'minimum_bedrooms' => 7]);

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&minimum_price=75000&minimum_bedrooms=7',
            $subject->decreaseMinimumPriceByPercentage(25)->url()
        );
    }

    public function testTheMaximumPriceCanBeReturned()
    {
        $subject = new SearchExpander(null, ['maximum_price' => 250000]);

        $this->assertEquals(250000, $subject->maximumPrice());
    }

    public function testTheMaximumPriceReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->maximumPrice());
    }

    public function testTheMaximumPriceCriteriaIsIncreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(null, ['maximum_price' => 100000]);

        $this->assertEquals(120000, $subject->increaseMaximumPriceByPercentage(20)->maximumPrice());
    }

    public function testTheMaximumPriceCriteriaIsIncreasedByASpecifiedPercentageAndRoundsUpToTheClosestInteger()
    {
        $subject = new SearchExpander(null, ['maximum_price' => 99997]);

        $this->assertEquals(119997, $subject->increaseMaximumPriceByPercentage(20)->maximumPrice());
    }

    public function testTheMaximumPriceCriteriaIsIgnoredWhenTryingToIncreaseItByPercentageIfItWasNotSuppliedInTheSearchCriteria()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->increaseMaximumPriceByPercentage(20)->maximumPrice());
    }

    public function testMoreExpensivePropertiesCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_price' => 250000, 'minimum_bedrooms' => 3]);

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_price=312500&minimum_bedrooms=3',
            $subject->increaseMaximumPriceByPercentage(25)->url()
        );
    }

    public function testTheMinimumBedroomsCanBeReturned()
    {
        $subject = new SearchExpander(null, ['minimum_bedrooms' => 3]);

        $this->assertEquals(3, $subject->minimumBedrooms());
    }

    public function testTheMinimumBedroomsReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->minimumBedrooms());
    }

    public function testTheMinimumBedroomsCriteriaIsDecreasedByACertainNumber()
    {
        $subject = new SearchExpander(null, ['minimum_bedrooms' => 3]);

        $this->assertEquals(1, $subject->decreaseMinimumBedroomsByNumber(2)->minimumBedrooms());
    }

    public function testTheMinimumBedroomsCriteriaIsNotDecreasedIfTheMinimumNumberIsAlready0()
    {
        $subject = new SearchExpander(null, ['minimum_bedrooms' => 0]);

        $this->assertEquals(0, $subject->decreaseMinimumBedroomsByNumber(2)->minimumBedrooms());
    }

    public function testPropertiesWithLessBedroomsCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_price' => 250000, 'minimum_bedrooms' => 3]);

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_price=250000&minimum_bedrooms=2',
            $subject->decreaseMinimumBedroomsByNumber(1)->url()
        );
    }

    public function testTheMaximumBedroomsCanBeReturned()
    {
        $subject = new SearchExpander(null, ['maximum_bedrooms' => 3]);

        $this->assertEquals(3, $subject->maximumBedrooms());
    }

    public function testTheMaximumBedroomsReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->maximumBedrooms());
    }

    public function testTheMaximumBedroomsCriteriaIsIncreasedByACertainNumber()
    {
        $subject = new SearchExpander(null, ['maximum_bedrooms' => 3]);

        $this->assertEquals(5, $subject->increaseMaximumBedroomsByNumber(2)->maximumBedrooms());
    }

    public function testPropertiesWithMoreBedroomsCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_price' => 250000, 'maximum_bedrooms' => 3]);

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_price=250000&maximum_bedrooms=4',
            $subject->increaseMaximumBedroomsByNumber(1)->url()
        );
    }

    public function testTheMinimumLandSizeCanBeReturned()
    {
        $subject = new SearchExpander(null, ['minimum_land_size' => 100000]);

        $this->assertEquals(100000, $subject->minimumLandSize());
    }

    public function testTheMinimumLandSizeReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->minimumLandSize());
    }

    public function testTheMinimumLandSizeCriteriaIsDecreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(null, ['minimum_land_size' => 100000]);

        $this->assertEquals(80000, $subject->decreaseMinimumLandSizeByPercentage(20)->minimumLandSize());
    }

    public function testTheMinimumLandSizeCriteriaIsDecreasedByASpecifiedPercentageAndRoundsDownToTheClosestInteger()
    {
        $subject = new SearchExpander(null, ['minimum_land_size' => 99997]);

        $this->assertEquals(79997, $subject->decreaseMinimumLandSizeByPercentage(20)->minimumLandSize());
    }

    public function testTheMinimumLandSizeCriteriaIsIgnoredWhenTryingToDecreaseItByPercentageIfItWasNotSuppliedInTheSearchCriteria()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->decreaseMinimumLandSizeByPercentage(20)->minimumLandSize());
    }

    public function testPropertiesWithLessLandCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'minimum_land_size' => 100000, 'minimum_bedrooms' => 7]);

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&minimum_land_size=75000&minimum_bedrooms=7',
            $subject->decreaseMinimumLandSizeByPercentage(25)->url()
        );
    }

    public function testTheMaximumLandSizeCanBeReturned()
    {
        $subject = new SearchExpander(null, ['maximum_land_size' => 250000]);

        $this->assertEquals(250000, $subject->maximumLandSize());
    }

    public function testTheMaximumLandSizeReturnsNullIfNotSet()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->maximumLandSize());
    }

    public function testTheMaximumLandSizeCriteriaIsIncreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(null, ['maximum_land_size' => 100000]);

        $this->assertEquals(120000, $subject->increaseMaximumLandSizeByPercentage(20)->maximumLandSize());
    }

    public function testTheMaximumLandSizeCriteriaIsIncreasedByASpecifiedPercentageAndRoundsUpToTheClosestInteger()
    {
        $subject = new SearchExpander(null, ['maximum_land_size' => 99997]);

        $this->assertEquals(119997, $subject->increaseMaximumLandSizeByPercentage(20)->maximumLandSize());
    }

    public function testTheMaximumLandSizeCriteriaIsIgnoredWhenTryingToIncreaseItByPercentageIfItWasNotSuppliedInTheSearchCriteria()
    {
        $subject = new SearchExpander(null, []);

        $this->assertEquals(null, $subject->increaseMaximumLandSizeByPercentage(20)->maximumLandSize());
    }

    public function testPropertiesWithMoreLandCanBeFoundWithTheOtherSearchCriteriaRemainingTheSame()
    {
        $subject = new SearchExpander('/sale-advert-search', ['title_en_any' => 'lake', 'maximum_land_size' => 100000, 'minimum_bedrooms' => 7]);

        $this->assertEquals(
            '/sale-advert-search?title_en_any=lake&maximum_land_size=125000&minimum_bedrooms=7',
            $subject->increaseMaximumLandSizeByPercentage(25)->url()
        );
    }

    public function testSearchCriteriaCanBeResetToItsOriginalValues()
    {
        $subject = new SearchExpander('/sale-advert-search', ['minimum_price' => 100000, 'maximum_price' => 200000]);

        $subject->increaseMaximumPriceByPercentage(25);
        $subject->decreaseMinimumPriceByPercentage(25);

        $this->assertEquals(75000, $subject->minimumPrice());
        $this->assertEquals(250000, $subject->maximumPrice());

        $this->assertEquals(100000, $subject->reset()->minimumPrice());
        $this->assertEquals(200000, $subject->reset()->maximumPrice());
    }
}
