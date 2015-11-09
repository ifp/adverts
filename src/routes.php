<?php

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::post('/sale-advert-search', 'SaleAdvertSearchController@index');
});


/*
class InputParser
{
    public function __construct(Array $request, $mapping)
    {
        $this->request = $request;
        $this->mapping_ifp = ['pmn' => 'minimum_price',
            'ps' => 'start_page',
            'pl' => 'page_size',
            's' => 'sort_by',
            'o' => 'sort_direction'];

        $this->mapping_connexion = ['pmn' => 'minimum_price',
            'ps' => 'start_page',
            'pl' => 'page_size',
            's' => 'sort_by',
            'o' => 'sort_direction'];
    }

    public function mapClientToApi()
    {
        //per page, current page
        //return array_map(function($param));
    }
}

class FrenchPropertySearch
{
    private $input_parser;

    public function __construct(InputParser $input_parser, $domain = 'https://search.french-property.com')
    {
        $this->input_parser = $input_parser;
        $this->http_client = new Client;
    }

    public function search($input)
    {
        $query_params = $this->input_parser->parseInput($input);

        $response = $this->http_client->get($this->domain, [
            'query' => $query_params,
        ]);

        return new SearchResults(json_decode((string) $response, true));
    }

}

class SearchResults
{
    private $meta;
    private $links;
    private $data;

    private $pagination;

    public function __construct($array_results)
    {
        $this->meta = $array_results['meta'];
        $this->links = $array_results['links'];
        $this->data = $array_results['data'];

        $meta = [
            'search_criteria' => $criteria->toArray(),
            'results' => [
                'total'         => $results->total(),
                'starting_from' => $results->startingFrom(),
                'finishing_at'  => $results->finishingAt(),
                'current_page'  => $results->currentPage(),
                'total_pages'   => $results->totalPages(),
            ],
        ];
    }

    public function getTotalPages()
    {
        return $this->links['total'];
    }

}
*/
