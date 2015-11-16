<?php

namespace IFP\Adverts;

use Exception;

class InvalidSearchCriteriaException extends Exception
{
    private $guzzle_exception;

    public function __construct($guzzle_exception)
    {
        $this->guzzle_exception = $guzzle_exception;
    }

    public function getErrors()
    {
        return json_decode((string) $this->guzzle_exception->getResponse()->getBody(), true)['errors'];
    }
}
