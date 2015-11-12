<?php

namespace IFP\SaleAdvertSearch;

class InputParser
{
    private $input_parameters;
    private $output_parameters = [];
    private $mapping;

    public function __construct($input_parameters, $mapping)
    {
        $this->input_parameters = $input_parameters;
        $this->mapping = $mapping;
    }

    public function mapParameters()
    {
        array_map(function($key, $value) {
            $this->output_parameters[$this->mapping[$key]] = $value;
        }, array_keys($this->input_parameters), $this->input_parameters);

        return $this->output_parameters;
    }
}
