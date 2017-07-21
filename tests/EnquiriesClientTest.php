<?php

use IFP\Adverts\EnquiriesClient;
use IFP\Adverts\Exceptions\InvalidApiTokenException;

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

    public function testItThrowsClientExceptionWhenReponseCodeNot401()
    {
        $form_data = $this->exampleEnquiryFormData();

        $guzzle_exception_double = new ClientExceptionDouble(400, 'something happened');

        $mock_guzzle_client = Mockery::mock()
            ->shouldReceive('post')->once()->with('api/enquiries', ['json' => $form_data])
            ->andThrow($guzzle_exception_double)
            ->getMock();

        $enquiries_client = new EnquiriesClient($mock_guzzle_client);

        try {
            $enquiries_client->send($form_data);
        } catch (Exception $e) {
            $this->assertEquals($guzzle_exception_double, $e);
            return;
        }

        $this->fail("Expected ClientException not thrown");
    }

    public function testItThrowsOtherExceptions()
    {
        $form_data = $this->exampleEnquiryFormData();

        $some_exception = new InvalidArgumentException("you made a mistake somewhere");

        $mock_guzzle_client = Mockery::mock()
            ->shouldReceive('post')->once()->with('api/enquiries', ['json' => $form_data])
            ->andThrow($some_exception)
            ->getMock();

        $enquiries_client = new EnquiriesClient($mock_guzzle_client);

        try {
            $enquiries_client->send($form_data);
        } catch (Exception $e) {
            $this->assertEquals($some_exception, $e);
            return;
        }

        $this->fail("Expected InvalidArgumentException not thrown");
    }
}
