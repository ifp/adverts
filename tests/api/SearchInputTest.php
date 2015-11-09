<?php

namespace IFP\SaleAdvertSearch\Tests;

class SearchInputTest extends \TestCase
{
    public function testItCanReceiveAndReturnInput()
    {
        //$this->withoutMiddleware();

        $this->visit('/')
            ->press('Search')
            ->seeJson(json_decode('{"errors":[{"title":"An error occurred on the server","detail":"Something went wrong, we are looking into it."}]}', true));
    }
}
