<?php

namespace IFP\Adverts\Tests\Helpers;

use Exception;
use Mockery;

class MockeryHelper
{
    public static function expectedParameterEquals($parameter_value_expected)
    {
        return Mockery::on(function($parameter_value) use ($parameter_value_expected) {
            if($parameter_value !== $parameter_value_expected) {
                throw new Exception("Expected parameter: '$parameter_value_expected', got '$parameter_value'");
            }
            return true;
        });
    }

    public static function expectedParameterContains($parameter_substring_expected)
    {
        return Mockery::on(function($parameter_value) use ($parameter_substring_expected) {
            if(strpos($parameter_value, $parameter_substring_expected) === false) {
                throw new Exception("Expected parameter to contain: '$parameter_substring_expected', not found in '$parameter_value'");
            }
            return true;
        });
    }
}