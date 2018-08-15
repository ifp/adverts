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

    public function testItCanSendAnEnquiryAndAddsTheUserId()
    {
        $form_data = $this->exampleEnquiryFormData();

        $mock_guzzle_client = new class {
            public $post_call_arguments = [];
            public function post($url, $options) {
                $this->post_call_arguments[] = [
                    'url' => $url,
                    'options' => $options
                ];
            }
        };

        $enquiries_client = new EnquiriesClient($mock_guzzle_client);
        $enquiries_client->send($form_data);

        $this->assertEquals(1, count($mock_guzzle_client->post_call_arguments));

        $expected_options = [
            'json' => $form_data
        ];
        // Null for the moment, as user accounts aren't integrated yet
        $expected_options['json']['enquirer']['user_id'] = null;
        $this->assertEquals('api/enquiries', $mock_guzzle_client->post_call_arguments[0]['url']);
        $this->assertEquals($expected_options, $mock_guzzle_client->post_call_arguments[0]['options']);
    }

    public function testItThrowsTheCorrectExceptionWhenTheApiTokenIsInvalid()
    {
        $form_data = $this->exampleEnquiryFormData();
        $expected_form_data = $form_data;
        $expected_form_data['enquirer']['user_id'] = null;

        $guzzle_exception_double = new ClientExceptionDouble(401, '{"errors":[{"title":"Authentication failed","detail":"Your API key is invalid"}]}');

        $mock_guzzle_client = Mockery::mock()
            ->shouldReceive('post')->once()->with('api/enquiries', ['json' => $expected_form_data])
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

        $expected_form_data = $form_data;
        $expected_form_data['enquirer']['user_id'] = null;
        $mock_guzzle_client = Mockery::mock()
            ->shouldReceive('post')->once()->with('api/enquiries', ['json' => $expected_form_data])
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

        $expected_form_data = $form_data;
        $expected_form_data['enquirer']['user_id'] = null;
        $mock_guzzle_client = Mockery::mock()
            ->shouldReceive('post')->once()->with('api/enquiries', ['json' => $expected_form_data])
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
