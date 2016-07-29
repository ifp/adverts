<?php

namespace IFP\Adverts\Sales;

use IFP\Adverts\Currency;
use IFP\Adverts\LandSize;

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

    private function roundNumberAbove100To2SignificantDigits($number_above_100)
    {
        $multiply_by_10_count = 0;
        while($number_above_100 > 100) {
            $number_above_100 /= 10;
            $multiply_by_10_count++;
        }

        $number_above_100 = round($number_above_100);

        $number_above_100 *= pow(10, $multiply_by_10_count);

        return $number_above_100;
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
            $this->search_criteria['maximum_price'] = $this->roundNumberAbove100To2SignificantDigits($this->search_criteria['maximum_price']);
        }
        if(isset($this->search_criteria['minimum_price'])) {
            $this->search_criteria['minimum_price'] = $this->roundNumberAbove100To2SignificantDigits($this->search_criteria['minimum_price']);
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

    private function countTrueValues($booleans)
    {
        $count = 0;

        return $count;
    }

    public function getExpansionOptions()
    {
        //dd($this->search_criteria);

        $expansion_options = []; //text => url

        $criteria_set_count = count(array_filter([$this->hasPriceCriteria(), $this->hasBedroomCriteria(), $this->hasLandCriteria()
            , $this->hasLocationCriteria(), $this->hasKeywordCriteria()]));

        // Multiple criteria sets - suggest reduce to just one
        if($criteria_set_count > 1) {
            if($this->reset()->hasPriceCriteriaWhichCanBeExpanded()) {
                $budget_options_only = $this->stripAllCriteriaExceptBudgets()->increaseMaximumPriceByPercentage(25)->decreaseMinimumPriceByPercentage(25)->roundBudgets();
                $expansion_options[$budget_options_only->budgetOnlyOptionText()] = $budget_options_only->url();
            }

            if($this->reset()->hasBedroomCriteria()) {
                $bedroom_options_only = $this->stripAllCriteriaExcept(['minimum_bedrooms', 'maximum_bedrooms', 'title_en_any', 'currency']);
                $expansion_options[$bedroom_options_only->bedroomsOnlyOptionText()] = $bedroom_options_only->url();
            }

            if($this->reset()->hasLandCriteriaWhichCanBeExpanded()) {
                $land_options_only = $this->stripAllCriteriaExceptLand()->increaseMaximumLandSizeByPercentage(25)->decreaseMinimumLandSizeByPercentage(25);
                $expansion_options[$land_options_only->landOnlyOptionText()] = $land_options_only->url();
            }

            if($this->reset()->hasLocationCriteria()) {
                $location_options_only = $this->stripAllCriteriaExcept(['regions', 'departments', 'title_en_any', 'currency']);
                $expansion_options[$location_options_only->locationsOnlyOptionText()] = $location_options_only->url();
            }

            if($this->reset()->hasKeywordCriteria()) {
                $location_options_only = $this->stripAllCriteriaExcept(['keywords_en_any', 'title_en_any', 'currency']);
                $expansion_options[$location_options_only->keywordsOnlyOptionText()] = $location_options_only->url();
            }
        }

        // Single criteria only - suggest to remove
        if($criteria_set_count == 1) {
            if($this->reset()->hasPriceCriteria()) {
                $expansion_options['Remove your price criteria'] = $this->stripAllCriteriaExcept(['currency', 'title_en_any'])->url();
            }
            if($this->reset()->hasBedroomCriteria()) {
                $expansion_options['Remove your bedroom criteria'] = $this->stripAllCriteriaExcept(['currency', 'title_en_any'])->url();
            }
            if($this->reset()->hasLandCriteria()) {
                $expansion_options['Remove your land criteria'] = $this->stripAllCriteriaExcept(['currency', 'title_en_any'])->url();
            }
            if($this->reset()->hasLocationCriteria()) {
                $expansion_options['Remove your location criteria'] = $this->stripAllCriteriaExcept(['currency', 'title_en_any'])->url();
            }
            if($this->reset()->hasKeywordCriteria()) {
                $expansion_options['Remove your keyword criteria'] = $this->stripAllCriteriaExcept(['currency', 'title_en_any'])->url();
            }
        }

        return $expansion_options;
    }

    private function hasPriceCriteria()
    {
        return isset($this->search_criteria['minimum_price']) || isset($this->search_criteria['maximum_price']);
    }

    private function minPriceEuros()
    {
        if(!isset($this->search_criteria['currency'])) {
            $this->search_criteria['currency'] = 'EUR';
        }
        return $this->currency->convertToEuros($this->search_criteria['minimum_price'], $this->search_criteria['currency']);
    }
    private function maxPriceEuros()
    {
        if(!isset($this->search_criteria['currency'])) {
            $this->search_criteria['currency'] = 'EUR';
        }
        return $this->currency->convertToEuros($this->search_criteria['maximum_price'], $this->search_criteria['currency']);
    }

    private function hasPriceCriteriaWhichCanBeExpanded()
    {
        //If max price specified only
        if(isset($this->search_criteria['maximum_price']) && !isset($this->search_criteria['minimum_price'])) {
            //If max price too low, cannot expand
            if($this->maxPriceEuros() < $this->LOWEST_ALLOWED_MAX_PRICE_EUROS) {
                return false;
            }
        }

        //If min price specified only
        if(isset($this->search_criteria['minimum_price']) && !isset($this->search_criteria['maximum_price'])) {
            //If min price too high, cannot expand
            if($this->minPriceEuros() > $this->HIGHEST_ALLOWED_MIN_PRICE_EUROS) {
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
    private function hasLandCriteria()
    {
        return isset($this->search_criteria['minimum_land_size']) || isset($this->search_criteria['maximum_land_size']);
    }

    private function minLandHectares()
    {
        $land_size = new LandSize();
        $land_size->from($this->search_criteria['minimum_land_size'], $this->search_criteria['land_size_unit']);

        return $land_size->toHectares()->value();
    }

    private function hasLandCriteriaWhichCanBeExpanded()
    {
        //If min land specified only
        if(isset($this->search_criteria['minimum_land_size']) && !isset($this->search_criteria['maximum_land_size'])) {

            //If min land too high, cannot expand
            if($this->minLandHectares() > $this->HIGHEST_ALLOWED_MIN_LAND_HECTARES) {
                return false;
            }
        }

        return $this->hasLandCriteria();
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
            if($this->maxPriceEuros() < $this->LOWEST_ALLOWED_MAX_PRICE_EUROS) {
                unset($this->search_criteria['maximum_price']);
            }
        }

        if(isset($this->search_criteria['minimum_price'])) {
            if($this->minPriceEuros() > $this->HIGHEST_ALLOWED_MIN_PRICE_EUROS) {
                unset($this->search_criteria['minimum_price']);
            }
        }

        return $this;
    }

    private function stripAllCriteriaExceptLand()
    {
        $this->stripAllCriteriaExcept(['minimum_land_size', 'maximum_land_size', 'land_size_unit', 'currency', 'title_en_any']); //title is temporary until advert checker up and running

        if(isset($this->search_criteria['minimum_land_size'])) {
            if($this->minLandHectares() > $this->HIGHEST_ALLOWED_MIN_LAND_HECTARES) {
                unset($this->search_criteria['minimum_land_size']);
            }
        }
        return $this;
    }

    // UNTESTED
    private function budgetOnlyOptionText()
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
    private function landOnlyOptionText()
    {
        $land_unit = mb_convert_case($this->search_criteria['land_size_unit'], MB_CASE_LOWER);

        // maximum only
        if(!isset($this->search_criteria['minimum_land_size'])) {
            return "Search only for " . $this->search_criteria['maximum_land_size'] . " " . $land_unit . " land maximum";
        }

        // minimum only
        if(!isset($this->search_criteria['maximum_land_size'])) {
            return "Search only for " . $this->search_criteria['minimum_land_size'] . " " . $land_unit . " land minimum";
        }

        return "Search only for " . $this->search_criteria['minimum_land_size'] . " - " . $this->search_criteria['maximum_land_size'] . " " . $land_unit . " land";
    }

    // UNTESTED
    private function bedroomsOnlyOptionText()
    {
        //print_r($this->search_criteria);

        // maximum only
        if(!isset($this->search_criteria['minimum_bedrooms'])) {
            return 'Search only for ' . $this->search_criteria['maximum_bedrooms'] . ' bedrooms maximum';
        }

        // minimum only
        if(!isset($this->search_criteria['maximum_bedrooms'])) {
            return 'Search only for ' . $this->search_criteria['minimum_bedrooms'] . ' bedrooms minimum';
        }

        // The same
        if($this->search_criteria['minimum_bedrooms'] == $this->search_criteria['maximum_bedrooms']) {
            return 'Search only for ' . $this->search_criteria['maximum_bedrooms'] . ' bedrooms';
        }

        return 'Search only for ' . $this->search_criteria['minimum_bedrooms'] . ' - ' . $this->search_criteria['maximum_bedrooms'] . ' bedrooms';
    }

    //UNTESTED
    private function locationsOnlyOptionText()
    {
        $location_option_text = 'Search only for ';

        $regions_specified = false;
        $departments_specified = false;

        if(isset($this->search_criteria['regions'])) {
            $regions_specified = (count($this->search_criteria['regions']) > 0);
        }
        if(isset($this->search_criteria['departments'])) {
            $departments_specified = (count($this->search_criteria['departments']) > 0);
        }

        if($regions_specified) {
            $region_names = array_map(function($region_tag) {
                // Temporary hack until we have an object to convert between region names and region tags
                return mb_convert_case($region_tag, MB_CASE_UPPER);
            }, $this->search_criteria['regions']);

            $location_option_text .= implode(', ', $region_names );
        }
        if($regions_specified && $departments_specified) {
            $location_option_text .= ' and ';
        }
        if($departments_specified) {
            $department_names = array_map(function($department_tag) {
                // Temporary hack until we have an object to convert between region names and region tags
                return mb_convert_case($department_tag, MB_CASE_UPPER);
            }, $this->search_criteria['departments']);

            $location_option_text .= implode(', ', $department_names);
        }

        return $location_option_text;
    }

    private function keywordsOnlyOptionText()
    {
        $keyword_option_text = 'Search only for keywords: ' . $this->search_criteria['keywords_en_any'];

        return $keyword_option_text;
    }

    public function removeCriteriaUrl()
    {
        return $this->stripAllCriteriaExcept(['title_en_any', 'currency'])->url();
    }
}
