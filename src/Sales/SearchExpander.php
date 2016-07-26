<?php

namespace IFP\Adverts\Sales;

class SearchExpander
{
    use QueryStringTrait;

    private $base_url;
    private $search_criteria;
    private $original_search_criteria;

    public function __construct($base_url, $search_criteria)
    {
        $this->base_url = $base_url;
        $this->search_criteria = $search_criteria;
        $this->original_search_criteria = $search_criteria;
    }

    public function reset()
    {
        $this->search_criteria = $this->original_search_criteria;
        return $this;
    }

    public function minimumPrice()
    {
        return isset($this->search_criteria['minimum_price']) ? $this->search_criteria['minimum_price'] : null;
    }

    public function maximumPrice()
    {
        return isset($this->search_criteria['maximum_price']) ? $this->search_criteria['maximum_price'] : null;
    }

    public function minimumBedrooms()
    {
        return isset($this->search_criteria['minimum_bedrooms']) ? $this->search_criteria['minimum_bedrooms'] : null;
    }

    public function maximumBedrooms()
    {
        return isset($this->search_criteria['maximum_bedrooms']) ? $this->search_criteria['maximum_bedrooms'] : null;
    }

    public function minimumLandSize()
    {
        return isset($this->search_criteria['minimum_land_size']) ? $this->search_criteria['minimum_land_size'] : null;
    }

    public function maximumLandSize()
    {
        return isset($this->search_criteria['maximum_land_size']) ? $this->search_criteria['maximum_land_size'] : null;
    }

    public function decreaseMinimumPriceByPercentage($percentage)
    {
        if (isset($this->search_criteria['minimum_price'])) {
            $this->search_criteria['minimum_price'] = (int)floor($this->search_criteria['minimum_price'] * (1 - ($percentage / 100)));
        }
        return $this;
    }

    public function increaseMaximumPriceByPercentage($percentage)
    {
        if (isset($this->search_criteria['maximum_price'])) {
            $this->search_criteria['maximum_price'] = (int)ceil($this->search_criteria['maximum_price'] * ((100 + $percentage) / 100));
        }
        return $this;
    }

    public function decreaseMinimumBedroomsByNumber($amount)
    {
        if (isset($this->search_criteria['minimum_bedrooms']) && ($this->search_criteria['minimum_bedrooms'] > 0)) {
            $this->search_criteria['minimum_bedrooms'] = (int)($this->search_criteria['minimum_bedrooms']-$amount);
        }
        return $this;
    }

    public function increaseMaximumBedroomsByNumber($amount)
    {
        if (isset($this->search_criteria['maximum_bedrooms'])) {
            $this->search_criteria['maximum_bedrooms'] = (int)($this->search_criteria['maximum_bedrooms']+$amount);
        }
        return $this;
    }

    public function decreaseMinimumLandSizeByPercentage($percentage)
    {
        if (isset($this->search_criteria['minimum_land_size'])) {
            $this->search_criteria['minimum_land_size'] = (int)floor($this->search_criteria['minimum_land_size'] * (1 - ($percentage / 100)));
        }
        return $this;
    }

    public function increaseMaximumLandSizeByPercentage($percentage)
    {
        if (isset($this->search_criteria['maximum_land_size'])) {
            $this->search_criteria['maximum_land_size'] = (int)ceil($this->search_criteria['maximum_land_size'] * ((100 + $percentage) / 100));
        }
        return $this;
    }

    public function removeKeywords()
    {
        $this->removeAnyKeywords();
        $this->removeAllKeywords();
        return $this;
    }

    public function removeAnyKeywords()
    {
        unset($this->search_criteria['keywords_en_any']);
        return $this;
    }

    public function removeAllKeywords()
    {
        unset($this->search_criteria['keywords_en_all']);
        return $this;
    }

    public function url()
    {
        return $this->base_url . '?' . $this->buildQueryString($this->search_criteria);
    }

    //UNTESTED
    public function tryToGuessBetterMaxPrice()
    {
        if(!isset($this->search_criteria['maximum_price'])) {
            return false;
        }

        //if maximum price is very low, suggest increasing it to a reasonable amount
        if ($this->search_criteria['maximum_price'] < 25000) {
            $this->search_criteria['maximum_price'] += 50000;
            return $this;
        }

        return false;
    }

    //UNTESTED
    public function tryToGuessBetterMinPrice()
    {
//        dd($this->search_criteria);

        if(!isset($this->search_criteria['minimum_price'])) {
            return false;
        }

        //if minimum price is very high, suggest lowering it
        if($this->search_criteria['minimum_price'] > 1000000) {
            $this->search_criteria['minimum_price'] = 900000;
            return $this;
        }

        if($this->search_criteria['minimum_price'] > 500000) {
            $this->search_criteria['minimum_price'] = 500000;
            return $this;
        }

        return false;
    }

    //UNTESTED - test with budget 50,000-51,000 and 300,000-320,000
    public function tryToGuessBetterPriceWindow()
    {
        if(!isset($this->search_criteria['minimum_price']) || !isset($this->search_criteria['maximum_price'])) {
            return false;
        }

        //If the price is too low, widening by 15% either way will not help
        if($this->search_criteria['maximum_price'] < 40000) {
            return false;
        }

        //If the price is too high, widening by 15% either way will not help
        if($this->search_criteria['maximum_price'] > 2000000) {
            return false;
        }

        $price_bracket = $this->search_criteria['maximum_price'] - $this->search_criteria['minimum_price'];
        //if the price window is less than 20% of the maximum price
        if($price_bracket < $this->search_criteria['maximum_price'] * 0.2) {
            $this->increaseMaximumPriceByPercentage(15);
            $this->decreaseMinimumPriceByPercentage(15);
            return $this;
        }

        return false;
    }

    //UNTESTED - assuming hectares
    public function tryToGuessBetterMinLand()
    {
        if(!isset($this->search_criteria['minimum_land_size'])) {
            return false;
        }

        //if minimum land size is very high, suggest lowering it
        if($this->search_criteria['minimum_land_size'] > 5) {
            $this->search_criteria['minimum_land_size'] = 5;
            return $this;
        }

        return false;
    }

    public function stripAllCriteriaExceptBudgets()
    {
        $budget_min = null;

        $budget_min = $this->search_criteria['minimum_price'];
        $budget_max = $this->search_criteria['maximum_price'];

        $this->search_criteria = [];
        $this->search_criteria['minimum_price'] = $budget_min;
        $this->search_criteria['maximum_price'] = $budget_max;

        return $this;
    }

//    public function stripAllCriteriaExceptBedroomsAndMakeThemReasonable()
//    {
//
//    }
//
//    public function stripAllCriteriaExceptLandAndMakeThemReasonable()
//    {
//
//    }
//
//    public function stripAllCriteriaExceptRegions()
//    {
//
//    }
//
//    public function stripAllCriteriaExceptDepartments()
//    {
//
//    }
//
//    public function stripAllCriteriaExceptKeywords()
//    {
//
//    }

    //budgets
    //bedrooms
    //land size
    //regions
    //departments
    //keywords
}
