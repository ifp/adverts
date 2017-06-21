<?php

namespace IFP\Adverts\Exceptions;

use Exception;

class InvalidPaginationDataException extends Exception
{
    private $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
