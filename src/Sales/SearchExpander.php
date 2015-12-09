<?php

namespace IFP\Adverts\Sales;

class SearchExpander
{
    private $search_criteria;

    public function __construct($search_criteria)
    {
        $this->search_criteria = $search_criteria;
    }

    public function reduceMinimumPrice()
    {
        return (int)floor($this->search_criteria['minimum_price'] * 0.75);
    }

    public function reduceMinimumPriceByPercentage($percentage)
    {
        return (int)floor($this->search_criteria['minimum_price'] * (1 - ($percentage / 100)));
    }

    public function increaseMaximumPrice()
    {
        return (int)ceil($this->search_criteria['maximum_price'] * 1.25);
    }

    public function increaseMaximumPriceByPercentage($percentage)
    {
        return (int)ceil($this->search_criteria['maximum_price'] * ((100 + $percentage) / 100));
    }

}
