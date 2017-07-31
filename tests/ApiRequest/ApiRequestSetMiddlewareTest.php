<?php

use Illuminate\Http\Request;
use Luminary\Exceptions\MultiException;
use Luminary\Services\ApiRequest\Exceptions\ForbiddenAttribute;
use Luminary\Services\ApiRequest\Exceptions\MissingDataAttribute;
use Luminary\Services\ApiRequest\Exceptions\MissingTypeAttribute;
use Luminary\Services\ApiRequest\Middleware\PostRequest;

class ApiRequestSetMiddlewareTest extends TestCase
{
    /**
     * Required request headers
     *
     * @var array
     */
    protected $headers = ['CONTENT_TYPE' => 'application/vnd.api+json'];

    /**
     * Test the injection of the api request
     *
     * @return void
     */
    public function testInjectApiRequest()
    {
        $self = $this;

        app()->post('/api', function(Request $request, \Luminary\Services\Testing\Requests\FormRequest $formRequest, \Luminary\Services\ApiRequest\ApiRequest $apiRequest) use($self) {
            $self->assertInstanceOf(\Luminary\Services\ApiRequest\ApiRequest::class, $request);
            $self->assertInstanceOf(\Luminary\Services\ApiRequest\ApiRequest::class, $formRequest);
            $self->assertInstanceOf(\Luminary\Services\ApiRequest\ApiRequest::class, $apiRequest);
        });

        $this->json('post', '/api', [
            'data' => [
                'type' => 'test',
                'attributes' => [
                    'first_name' => 'john',
                    'last_name' => 'smith'
                ],
                'relationships' => [
                    'customer' => [
                        'data' => [
                            'type' => 'people',
                            'id' => "1234"
                        ]
                    ]
                ]
            ]
        ],$this->headers);
    }
}
