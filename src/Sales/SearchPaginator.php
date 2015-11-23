<?php

namespace IFP\Adverts\Sales;

use IFP\Adverts\InvalidPaginationDataException;

class SearchPaginator
{
    use QueryStringTrait;

    private $base_url;
    private $search_criteria;
    private $results;
    private $errors;

    public function __construct($base_url, $search_criteria, $results)
    {
        $this->base_url = $base_url;
        $this->search_criteria = $search_criteria;

        $this->errors = [];

        $this->results = $results;

        $this->validateResults();
    }

    private function validateResults()
    {
        $fields = ['total', 'starting_from', 'finishing_at', 'current_page', 'total_pages'];

        array_map(function($field) {
            if(!isset($this->results[$field])) {
                $this->errors[] = [
                    "title" => "Invalid pagination data",
                    "detail" => "`${field}` was not set or is null.",
                ];
            } elseif(!is_numeric($this->results[$field])) {
                    $this->errors[] = [
                        "title" => "Invalid pagination data",
                        "detail" => "`${field}` was not numeric.",
                    ];
            } else {
                $this->results[$field] = (int)$this->results[$field];
            }
        }, $fields);

        if(count($this->errors) > 0) {
            throw new InvalidPaginationDataException($this->errors);
        }
    }

    public function total()
    {
        return $this->results['total'];
    }

    public function startingFrom()
    {
        return $this->results['starting_from'];
    }

    public function finishingAt()
    {
        return $this->results['finishing_at'];
    }

    public function currentPage()
    {
        return $this->results['current_page'];
    }

    public function totalPages()
    {
        return $this->results['total_pages'];
    }

    public function hasMorePages()
    {
        return $this->totalPages() > $this->currentPage();
    }

    public function hasLessPages()
    {
        return $this->currentPage() > 1;
    }

    public function nextPage()
    {
        if($this->currentPage() == $this->totalPages()) {
            return false;
        }
        return $this->currentPage() + 1;
    }

    public function previousPage()
    {
        if($this->currentPage() == 1) {
            return false;
        }
        return $this->currentPage() - 1;
    }

    public function firstPage()
    {
        return 1;
    }

    public function lastPage()
    {
        return $this->totalPages();
    }

    public function currentPageUrl()
    {
        return $this->makeFullUrl($this->currentPage());
    }

    public function firstPageUrl()
    {
        return $this->makeFullUrl($this->firstPage());
    }

    public function lastPageUrl()
    {
        return $this->makeFullUrl($this->lastPage());
    }

    public function nextPageUrl()
    {
        return $this->makeFullUrl($this->nextPage());
    }

    public function previousPageUrl()
    {
        return $this->makeFullUrl($this->previousPage());
    }

    public function nextPagesUrls($number_of_pages)
    {
        $urls = [];

        for($i = $this->currentPage()+1; $i <= $this->currentPage()+$number_of_pages; $i++)
        {
            if($i <= $this->totalPages()) {
                $urls[] = [
                    'page_number' => $i,
                    'page_type' => 'next',
                    'url' => $this->makeFullUrl($i),
                ];
            }
        }

        return $urls;
    }

    public function previousPagesUrls($number_of_pages)
    {
        $urls = [];

        for($i = $this->currentPage()-$number_of_pages; $i <=$this->currentPage()-1; $i++)
        {
            if($i >= $this->firstPage()) {
                $urls[] = [
                    'page_number' => $i,
                    'page_type' => 'previous',
                    'url' => $this->makeFullUrl($i),
                ];
            }
        }

        return $urls;
    }

    public function previousSetUrl($number_of_pages_in_scroll)
    {
        $previous_set_page_number = $this->currentPage() - $number_of_pages_in_scroll;

        $urls = [];

        if($previous_set_page_number >= 1) {
            $urls[] = [
                'page_number' => $previous_set_page_number,
                'page_type' => 'previous_set',
                'url' => $this->makeFullUrl($previous_set_page_number),
            ];
        }

        return $urls;
    }

    public function nextSetUrl($number_of_pages_in_scroll)
    {
        $next_set_page_number = $this->currentPage() + $number_of_pages_in_scroll;

        $urls = [];

        if($next_set_page_number <= $this->totalPages()) {
            $urls[] = [
                'page_number' => $next_set_page_number,
                'page_type' => 'next_set',
                'url' => $this->makeFullUrl($next_set_page_number),
            ];
        }

        return $urls;
    }

    public function scrollPagesUrls($number_of_pages_in_scroll)
    {
        $half_number_of_pages = (int)$number_of_pages_in_scroll/2;
        $number_of_previous_pages = $half_number_of_pages;
        $number_of_next_pages = $half_number_of_pages;

        if($this->currentPage() <= $half_number_of_pages) {
            $number_of_previous_pages = $this->currentPage()-1;
            $number_of_next_pages += ($half_number_of_pages - $number_of_previous_pages);
        }

        if($this->currentPage() >= ($this->totalPages() - $half_number_of_pages)) {
            $number_of_next_pages = $this->totalPages() - $this->currentPage();
            $number_of_previous_pages += ($half_number_of_pages - $number_of_next_pages);
        }

        $urls[] = [
            'page_number' => $this->firstPage(),
            'page_type' => 'first',
            'url' => $this->firstPageUrl(),
        ];

        $urls = array_merge($urls, $this->previousSetUrl($number_of_pages_in_scroll));

        $urls = array_merge($urls, $this->previousPagesUrls($number_of_previous_pages));

        $urls[] = [
            'page_number' => $this->currentPage(),
            'page_type' => 'current',
            'url' => $this->currentPageUrl(),
        ];

        $urls = array_merge($urls, $this->nextPagesUrls($number_of_next_pages));

        $urls = array_merge($urls, $this->nextSetUrl($number_of_pages_in_scroll));

        $urls[] = [
            'page_number' => $this->lastPage(),
            'page_type' => 'last',
            'url' => $this->lastPageUrl(),
        ];

        return $urls;
    }

    private function makeFullUrl($start_page)
    {
        if($start_page) {
            $query_vars = $this->search_criteria;
            $query_vars['start_page'] = $start_page;
            return $this->base_url. '?' . http_build_query($query_vars);
        }

        return false;
    }
}
