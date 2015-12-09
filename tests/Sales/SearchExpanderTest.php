<?php

use IFP\Adverts\Sales\SearchExpander;

class SearchExpanderTest extends PHPUnit_Framework_TestCase
{
    public function testTheMinimumPriceCriteriaIsReducedBy25Percent()
    {
        $subject = new SearchExpander(['minimum_price' => 100000]);

        $this->assertEquals(75000, $subject->reduceMinimumPrice());
    }

    public function testTheMinimumPriceCriteriaIsReducedBy25PercentAndRoundsDownToTheClosestInteger()
    {
        $subject = new SearchExpander(['minimum_price' => 99999]);

        $this->assertEquals(74999, $subject->reduceMinimumPrice());
    }

    public function testTheMinimumPriceCriteriaIsReducedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(['minimum_price' => 100000]);

        $this->assertEquals(80000, $subject->reduceMinimumPriceByPercentage(20));
    }

    public function testTheMinimumPriceCriteriaIsReducedByASpecifiedPercentageAndRoundsDownToTheClosestInteger()
    {
        $subject = new SearchExpander(['minimum_price' => 99997]);

        $this->assertEquals(79997, $subject->reduceMinimumPriceByPercentage(20));
    }

    public function testTheMaximumPriceCriteriaIsIncreasedBy25Percent()
    {
        $subject = new SearchExpander(['maximum_price' => 100000]);

        $this->assertEquals(125000, $subject->increaseMaximumPrice());
    }

    public function testTheMaximumPriceCriteriaIsIncreasedBy25PercentAndRoundsUpToTheClosestInteger()
    {
        $subject = new SearchExpander(['maximum_price' => 100003]);

        $this->assertEquals(125004, $subject->increaseMaximumPrice());
    }

    public function testTheMaximumPriceCriteriaIsIncreasedByASpecifiedPercentage()
    {
        $subject = new SearchExpander(['maximum_price' => 100000]);

        $this->assertEquals(120000, $subject->increaseMaximumPriceByPercentage(20));
    }

    public function testTheMaximumPriceCriteriaIsIncreasedByASpecifiedPercentageAndRoundsUpToTheClosestInteger()
    {
        $subject = new SearchExpander(['maximum_price' => 99997]);

        $this->assertEquals(119997, $subject->increaseMaximumPriceByPercentage(20));
    }

}
