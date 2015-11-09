<?php

namespace IFP\SaleAdvertSearch\Tests;

use IFP\SaleAdvertSearch\InputParser;

class InputParserTest extends \PHPUnit_Framework_TestCase
{
    public function testItMapsInputParametersIntoOutputParameters()
    {
        $input_parameters = [
            "pmn" => 100000,
            "pmx" => 200000,
            "bedrooms" => 3,
        ];

        $mapping = [
            'pmn' => 'minimum_price',
            'pmx' => 'maximum_price',
            'bedrooms' => 'minimum_bedrooms',
        ];

        $input_parser = new InputParser($input_parameters, $mapping);

        $output_parameters = [
            "minimum_price" => 100000,
            "maximum_price" => 200000,
            "minimum_bedrooms" => 3,
        ];

        $this->assertEquals($output_parameters, $input_parser->mapParameters());
    }
}
