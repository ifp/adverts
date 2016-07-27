<?php

namespace IFP\Adverts\Sales;

use IFP\Adverts\Currency;

class SearchExpander
{
    use QueryStringTrait;

    private $base_url;
    private $search_criteria;
    private $original_search_criteria;
    /** @var Currency */
    private $currency;

    private $LOWEST_ALLOWED_MAX_PRICE_EUROS = 50000;
    private $HIGHEST_ALLOWED_MIN_PRICE_EUROS = 900000;

    private $HIGHEST_ALLOWED_MIN_LAND_HECTARES = 50;

    private function round2SignificantDigits($number)
    {
        $multiply_by_10_count = 0;
        while($number > 100) {
            $number /= 10;
            $multiply_by_10_count++;
        }

        $number = round($number);

        $number *= pow(10, $multiply_by_10_count);

        return $number;
    }

    public function __construct($base_url, $search_criteria, $currency)
    {
        $this->base_url = $base_url;
        $this->search_criteria = $search_criteria;
        $this->original_search_criteria = $search_criteria;
        $this->currency = $currency;
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
    
    public function roundBudgets()
    {
        if(isset($this->search_criteria['maximum_price'])) {
            $this->search_criteria['maximum_price'] = $this->round2SignificantDigits($this->search_criteria['maximum_price']);
        }
        if(isset($this->search_criteria['minimum_price'])) {
            $this->search_criteria['minimum_price'] = $this->round2SignificantDigits($this->search_criteria['minimum_price']);
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

    public function getExpansionOptions()
    {
        //dd($this->search_criteria);

        $expansion_options = []; //text => url

        if($this->reset()->hasPriceCriteriaWhichCanBeExpanded()) {
            $budget_options_only = $this->stripAllCriteriaExceptBudgets()->increaseMaximumPriceByPercentage(25)->decreaseMinimumPriceByPercentage(25)->roundBudgets();
            $expansion_options[$budget_options_only->budgetOptionText()] = $budget_options_only->url();
        }

        if($this->reset()->hasBedroomCriteria()) {
            $bedroom_options_only = $this->stripAllCriteriaExceptBedrooms();
            $expansion_options[$bedroom_options_only->bedroomsOptionText()] = $bedroom_options_only->url();
        }

        if($this->reset()->hasLandCriteria()) {
            //TODO
        }

        if($this->reset()->hasLocationCriteria()) {
            //TODO
        }

        if($this->reset()->hasKeywordCriteria()) {
            //TODO
        }

        return $expansion_options;
    }

    private function hasPriceCriteria()
    {
        return isset($this->search_criteria['minimum_price']) || isset($this->search_criteria['maximum_price']);
    }

    private function hasPriceCriteriaWhichCanBeExpanded()
    {
        //If max price specified only
        if(isset($this->search_criteria['maximum_price']) && !isset($this->search_criteria['minimum_price'])) {
            //If max price too low, cannot expand
            $max_price_euros = $this->currency->convertToEuros($this->search_criteria['maximum_price'], $this->search_criteria['currency']);
            if($max_price_euros < $this->LOWEST_ALLOWED_MAX_PRICE_EUROS) {
                return false;
            }
        }

        //If min price specified only
        if(isset($this->search_criteria['minimum_price']) && !isset($this->search_criteria['maximum_price'])) {
            //If min price too high, cannot expand
            $min_price_euros = $this->currency->convertToEuros($this->search_criteria['minimum_price'], $this->search_criteria['currency']);
            if($min_price_euros > $this->HIGHEST_ALLOWED_MIN_PRICE_EUROS) {
                return false;
            }
        }

        return $this->hasPriceCriteria();
    }

    // UNTESTED
    private function hasBedroomCriteria()
    {
        return isset($this->search_criteria['minimum_bedrooms']) || isset($this->search_criteria['maximum_bedrooms']);
    }

    // UNTESTED
    public function hasLandCriteria()
    {
        return isset($this->search_criteria['minimum_land_size']) || isset($this->search_criteria['maximum_land_size']);
    }

    // UNTESTED
    public function hasLocationCriteria()
    {
        return isset($this->search_criteria['regions']) || isset($this->search_criteria['departments']);
    }

    // UNTESTED
    public function hasKeywordCriteria()
    {
        return isset($this->search_criteria['keywords_en_any']);
    }

    private function stripAllCriteriaExcept($criteria_keys)
    {
        $keys_array = [];
        foreach($criteria_keys as $criteria_key) {
            $keys_array[$criteria_key] = null;
        }

        $this->search_criteria = array_intersect_key($this->search_criteria, $keys_array);

        return $this;
    }

    private function stripAllCriteriaExceptBudgets()
    {
        $this->stripAllCriteriaExcept(['minimum_price', 'maximum_price', 'currency', 'title_en_any']); //title is temporary until advert checker up and running

        if(isset($this->search_criteria['maximum_price'])) {
            $max_price_euros = $this->currency->convertToEuros($this->search_criteria['maximum_price'], $this->search_criteria['currency']);
            if($max_price_euros < $this->LOWEST_ALLOWED_MAX_PRICE_EUROS) {
                unset($this->search_criteria['maximum_price']);
            }
        }

        if(isset($this->search_criteria['minimum_price'])) {
            $min_price_euros = $this->currency->convertToEuros($this->search_criteria['minimum_price'], $this->search_criteria['currency']);
            if($min_price_euros > $this->HIGHEST_ALLOWED_MIN_PRICE_EUROS) {
                unset($this->search_criteria['minimum_price']);
            }
        }

        return $this;
    }

    // UNTESTED
    private function budgetOptionText()
    {
        $this->currency->setCurrency($this->search_criteria['currency']);
        $currency_symbol = $this->currency->symbol();

        $min_budget_str = null;
        $max_budget_str = null;
        if(isset($this->search_criteria['minimum_price'])) {
            $min_budget_str = $currency_symbol . $this->currency->formatValue($this->search_criteria['minimum_price']);
        }
        if(isset($this->search_criteria['maximum_price'])) {
            $max_budget_str = $currency_symbol . $this->currency->formatValue($this->search_criteria['maximum_price']);
        }

        // maximum only
        if(!isset($this->search_criteria['minimum_price'])) {
            return "Search only for budget $max_budget_str maximum";
        }

        // minimum only
        if(!isset($this->search_criteria['maximum_price'])) {
            return "Search only for budget $min_budget_str minimum";
        }

        return "Search only for budget $min_budget_str - $max_budget_str";
    }

    // UNTESTED
    private function stripAllCriteriaExceptBedrooms()
    {
        return $this->stripAllCriteriaExcept(['minimum_bedrooms', 'maximum_bedrooms', 'title_en_any']); //title is temporary until advert checker up and running
    }

    // UNTESTED
    private function bedroomsOptionText()
    {
        // maximum only
        if(!isset($this->search_criteria['maximum_bedrooms'])) {
            return 'Search only for ' . $this->search_criteria['maximum_bedrooms'] . ' bedrooms maximum';
        }

        // minimum only
        if(!isset($this->search_criteria['minimum_bedrooms'])) {
            return 'Search only for ' . $this->search_criteria['minimum_bedrooms'] . ' bedrooms minimum';
        }

        return 'Search only for ' . $this->search_criteria['minimum_bedrooms'] . ' - ' . $this->search_criteria['maximum_bedrooms'] . ' bedrooms';
    }

//    public function stripAllCriteriaExceptLand()
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

    //
    // Andy says: I have left this commented code in below for possible future use
    //

//        //UNTESTED
//    public function tryToGuessBetterMaxPrice()
//    {
//        if(!isset($this->search_criteria['maximum_price'])) {
//            return false;
//        }
//
//        //if maximum price is very low, suggest increasing it to a reasonable amount
//        if ($this->search_criteria['maximum_price'] < 25000) {
//            $this->search_criteria['maximum_price'] += 50000;
//            return $this;
//        }
//
//        return false;
//    }
//
//    //UNTESTED
//    public function tryToGuessBetterMinPrice()
//    {
////        dd($this->search_criteria);
//
//        if(!isset($this->search_criteria['minimum_price'])) {
//            return false;
//        }

//        //if minimum price is very high, suggest lowering it
//        if($this->search_criteria['minimum_price'] > 1000000) {
//            $this->search_criteria['minimum_price'] = 900000;
//            return $this;
//        }
//
//        if($this->search_criteria['minimum_price'] > 500000) {
//            $this->search_criteria['minimum_price'] = 500000;
//            return $this;
//        }
//
//        return false;
//    }
//
//    //UNTESTED - e.g. test with budget 50,000-51,000 and 300,000-320,000
//    public function tryToGuessBetterPriceWindow()
//    {
//        if(!isset($this->search_criteria['minimum_price']) || !isset($this->search_criteria['maximum_price'])) {
//            return false;
//        }
//
//        //If the price is too low, widening by 15% either way will not help
//        if($this->search_criteria['maximum_price'] < 40000) {
//            return false;
//        }
//
//        //If the price is too high, widening by 15% either way will not help
//        if($this->search_criteria['maximum_price'] > 2000000) {
//            return false;
//        }
//
//        $price_bracket = $this->search_criteria['maximum_price'] - $this->search_criteria['minimum_price'];
//        //if the price window is less than 20% of the maximum price
//        if($price_bracket < $this->search_criteria['maximum_price'] * 0.2) {
//            $this->increaseMaximumPriceByPercentage(15);
//            $this->decreaseMinimumPriceByPercentage(15);
//            return $this;
//        }
//
//        return false;
//    }
//
//    //UNTESTED - assuming hectares
//    public function tryToGuessBetterMinLand()
//    {
//        if(!isset($this->search_criteria['minimum_land_size'])) {
//            return false;
//        }
//
//        //if minimum land size is very high, suggest lowering it
//        if($this->search_criteria['minimum_land_size'] > 5) {
//            $this->search_criteria['minimum_land_size'] = 5;
//            return $this;
//        }
//
//        return false;
//    }

}
