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

    public function url()
    {
        return $this->base_url . '?' . $this->buildQueryString($this->search_criteria);
    }
}
