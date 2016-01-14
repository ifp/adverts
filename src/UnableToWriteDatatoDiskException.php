<?php

namespace IFP\Adverts;

use Exception;

class UnableToWriteDataToDiskException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
