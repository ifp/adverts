<?php

namespace IFP\Adverts\Exceptions;

use Exception;

class StartPageOutOfBoundsException extends Exception
{
    private $error;

    public function __construct($error)
    {
        $this->error = $error;
    }

    public function lastPage()
    {
        return $this->error['meta']['total_pages'];
    }
}
