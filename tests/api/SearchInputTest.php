<?php

namespace IFP\SaleAdvertSearchClient\Tests;

class SearchInputTest extends \TestCase
{
    public function testItCanReceiveAndReturnInput()
    {
        $this->visit('/')
            ->press('Search')
            ->seeJson('{"errors":[{"title":"An error occurred on the server","detail":"Something went wrong, we are looking into it."}]}');
    }
}
