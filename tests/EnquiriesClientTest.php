<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use IFP\Adverts\EnquiriesClient;
use IFP\Adverts\InvalidApiTokenException;

class EnquiriesClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    private function exampleEnquiryFormData()
    {
        return [
            'enquirer' => [
                'forename' => 'Foofoo',
                'surname' => 'Bar'
            ],
            'enquiry' => [
                'message' => 'foo message'
            ]
        ];
    }

    public function testItCanSendAnEnquiry()
    {
        $form_data = $this->exampleEnquiryFormData();

        $mock_guzzle_client = Mockery::mock()
            ->shouldReceive('post')->once()->with('api/enquiries', ['json' => $form_data])
            ->getMock();

        $enquiries_client = new EnquiriesClient($mock_guzzle_client);
        $enquiries_client->send($form_data);
    }

    public function testItThrowsTheCorrectExceptionWhenTheApiTokenIsInvalid()
    {
        $form_data = $this->exampleEnquiryFormData();

        $guzzle_exception_double = new ClientExceptionDouble(401, '{"errors":[{"title":"Authentication failed","detail":"Your API key is invalid"}]}');

        $mock_guzzle_client = Mockery::mock()
            ->shouldReceive('post')->once()->with('api/enquiries', ['json' => $form_data])
            ->andThrow($guzzle_exception_double)
            ->getMock();

        $enquiries_client = new EnquiriesClient($mock_guzzle_client);

        try {
            $enquiries_client->send($form_data);
        } catch (InvalidApiTokenException $e) {
            return;
        }

        $this->fail("Expected InvalidApiTokenException not thrown");
    }
}