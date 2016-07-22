<?php

namespace IFP\Adverts\Tests\Helpers;

use Exception;
use Mockery;

class AssertHelper
{
    //Andy syas: Not in use yet - possibly useful function
    public static function assertThrowsException($phpunit_instance, $expected_exception_class, $callback)
    {
        try {
            $callback();
        } catch(Exception $exception) {
            $exception_thrown_class = get_class($exception);
            if($exception_thrown_class == $expected_exception_class) {
                return;
            }
            $phpunit_instance->fail("Expected $expected_exception_class, $exception_thrown_class was thrown");
            throw $exception;
        }

        $phpunit_instance->fail("Expected $expected_exception_class exception, no exception thrown");
    }
}