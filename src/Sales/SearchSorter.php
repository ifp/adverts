<?php

namespace IFP\Adverts\Sales;

class SearchSorter
{
    use QueryStringTrait;

    private $base_url;
    private $search_criteria;
    private $current_sort;

    public function __construct($base_url, $search_criteria)
    {
        $this->base_url = $base_url;
        $this->search_criteria = $search_criteria;
        $this->identifyCurrentSort();
        unset($this->search_criteria['sort_by']);
        unset($this->search_criteria['sort_direction']);
        unset($this->search_criteria['start_page']); // The SearchSorter makes links which start from the first page of the sorted results,
        unset($this->search_criteria['page_size']); // thus we drop the pagination criteria: start_page and page_size
    }

    private function identifyCurrentSort()
    {
        if(isset($this->search_criteria['sort_by']) && isset($this->search_criteria['sort_direction'])) {
            $this->current_sort = $this->search_criteria['sort_by'] . '_' . $this->search_criteria['sort_direction'];
        }
    }

    public function currentSort()
    {
        return $this->current_sort;
    }

    public function priceAscendingUrl()
    {
        return $this->sortUrl('price', 'asc');
    }

    public function priceDescendingUrl()
    {
        return $this->sortUrl('price', 'desc');
    }

    public function dateDescendingUrl()
    {
        return $this->sortUrl('date', 'desc');
    }

    public function landSizeDescendingUrl()
    {
        return $this->sortUrl('land_size', 'desc');
    }

    public function savedAtDescendingUrl()
    {
        return $this->sortUrl('saved_at', 'desc');
    }

    public function savedAtAscendingUrl()
    {
        return $this->sortUrl('saved_at', 'asc');
    }

    private function sortUrl($sort_by, $sort_direction)
    {
        $query_vars = $this->search_criteria;
        $query_vars['sort_by'] = $sort_by;
        $query_vars['sort_direction'] = $sort_direction;
        return $this->base_url . '?' . $this->buildQueryString($query_vars);
    }
}
