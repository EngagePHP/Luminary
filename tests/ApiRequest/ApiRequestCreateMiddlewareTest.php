<?php

use Illuminate\Http\Request;
use Luminary\Exceptions\MultiException;
use Luminary\Services\ApiRequest\Exceptions\ForbiddenAttribute;
use Luminary\Services\ApiRequest\Exceptions\MissingDataAttribute;
use Luminary\Services\ApiRequest\Exceptions\MissingTypeAttribute;
use Luminary\Services\ApiRequest\Middleware\PostRequest;

class ApiRequestCreateMiddlewareTest extends TestCase
{
    /**
     * The JsonApiRequest instance
     *
     * @var \Luminary\Services\ApiRequest\Middleware\PostRequest
     */
    protected $middleware;

    /**
     * Required request headers
     *
     * @var array
     */
    protected $headers = ['CONTENT_TYPE' => 'application/vnd.api+json'];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->middleware = new PostRequest;
    }

    /**
     * Test data attribute failure
     *
     * @return void
     */
    public function testDataAttributeMethodFails() :void
    {
        $this->expectException(MissingDataAttribute::class);
        $this->expectExceptionMessage('Missing data attribute for create request');

        $fail = ['datas' => []];

        $this->middleware->dataAttributeExists($fail);
    }

    /**
     * Test data attribute failure response
     *
     * @return void
     */
    public function testDataAttributeMethodFailureResponse() :void
    {
        app()->post('/api', function() {});

        $this->json('post', '/api', ['datas' => []],$this->headers);

        $expected = [
            'errors' => [
                [
                    'status' => 400,
                    'title' => 'Bad Request',
                    'detail' => 'Missing data attribute for create request',
                    'source' => [
                        'pointer' => '/data'
                    ]
                ]
            ]
        ];

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals($expected, $response);
    }

    /**
     * Test data attribute failure
     *
     * @return void
     */
    public function testTypeAttributeMethodFails() :void
    {
        $this->expectException(MissingTypeAttribute::class);
        $this->expectExceptionMessage('Missing the required type attribute for the data object');

        $fail = ['notit' => []];

        $this->middleware->typeAttributeExists($fail);
    }

    /**
     * Test data attribute failure response
     *
     * @return void
     */
    public function testTypeAttributeMethodFailureResponse() :void
    {
        app()->post('/api', function() {});

        $this->json('post', '/api', ['data' => []],$this->headers);

        $expected = [
            'errors' => [
                [
                    'status' => 400,
                    'title' => 'Bad Request',
                    'detail' => 'Missing the required type attribute for the data object',
                    'source' => [
                        'pointer' => '/data/type'
                    ]
                ]
            ]
        ];

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals($expected, $response);
    }

    /**
     * Test data attribute failure
     *
     * @return void
     */
    public function testAcceptedAttributesMethodFails() :void
    {
        $this->expectException(MultiException::class);

        $fail = ['notit' => []];

        $this->middleware->hasAcceptedAttributes($fail, ['type', 'attributes', 'relationships']);
    }

    /**
     * Test data attribute failure response
     *
     * @return void
     */
    public function testAcceptedAttributesMethodFailureResponse() :void
    {
        app()->post('/api', function() {});

        $this->json('post', '/api', ['data' => ['type' => 'test', 'not-it' => '', 'unacceptable' => '']],$this->headers);

        $expected = [
            'errors' => [
                [
                    'status' => 400,
                    'title' => 'Bad Request',
                    'detail' => 'Only type, attributes, and relationships are accepted in the data object.',
                    'source' => [
                        'pointer' => '/data/not-it'
                    ]
                ],
                [
                    'status' => 400,
                    'title' => 'Bad Request',
                    'detail' => 'Only type, attributes, and relationships are accepted in the data object.',
                    'source' => [
                        'pointer' => '/data/unacceptable'
                    ]
                ]
            ]
        ];

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals($expected, $response);
    }

    /**
     * Test forbidden attribute failure
     *
     * @return void
     */
    public function testForbiddenAttributeMethodFails() :void
    {
        $this->expectException(ForbiddenAttribute::class);
        $this->expectExceptionMessage('The attribute supplied is forbidden for this request');

        $fail = ['attributes' => ['id' => '1234']];

        $this->middleware->hasForbiddenAttribute('id', $fail);
    }

    /**
     * Test forbidden attribute failure response
     *
     * @return void
     */
    public function testForbiddenAttributeMethodFailureResponse() :void
    {
        app()->post('/api', function() {});

        $this->json('post', '/api', ['data' => [
            'type' => 'test',
            'attributes' => ['id' => '1234']
        ]],$this->headers);

        $expected = [
            'errors' => [
                [
                    'status' => 403,
                    'title' => 'Forbidden Attribute',
                    'detail' => 'The attribute supplied is forbidden for this request',
                    'source' => [
                        'pointer' => '/data/attributes/id'
                    ]
                ]
            ]
        ];

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals($expected, $response);
    }
}
