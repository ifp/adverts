<?php

namespace IFP\SaleAdvertSearchClient\Tests;

class SearchInputTest extends \TestCase
{
    public function testItCanReceiveAndReturnInput()
    {
        $this->visit('/')
            ->press('Search')
            ->see('Whoops, looks like something went wrong.');
    }
}
