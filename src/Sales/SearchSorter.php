<?php

namespace IFP\Adverts\Sales;

class SearchSorter
{
    use QueryStringTrait;

    private $base_url;
    private $search_criteria;

    public function __construct($base_url, $search_criteria)
    {
        $this->base_url = $base_url;
        $this->search_criteria = $search_criteria;
        unset($this->search_criteria['start_page']);
        unset($this->search_criteria['page_size']);
        unset($this->search_criteria['sort_by']);
        unset($this->search_criteria['sort_direction']);
    }

    public function priceAscendingUrl()
    {
        $query_vars = $this->search_criteria;
        $query_vars['sort_by'] = 'price';
        $query_vars['sort_direction'] = 'asc';
        return $this->base_url . '?' . $this->buildQueryString($query_vars);
    }

    public function priceDescendingUrl()
    {
        $query_vars = $this->search_criteria;
        $query_vars['sort_by'] = 'price';
        $query_vars['sort_direction'] = 'desc';
        return $this->base_url . '?' . $this->buildQueryString($query_vars);
    }

    public function dateDescendingUrl()
    {
        $query_vars = $this->search_criteria;
        $query_vars['sort_by'] = 'date';
        $query_vars['sort_direction'] = 'desc';
        return $this->base_url . '?' . $this->buildQueryString($query_vars);
    }
}
