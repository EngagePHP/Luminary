<?php

class QueryMiddlewareTest extends TestCase
{
    use Luminary\Services\Testing\BaseTestingTrait;

    /**
     * Query String parameters to build
     *
     * @var array
     */
    protected $queryString = [
        'include' => 'customers,locations'
    ];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpRoutes();
        $this->setUpUrl();
        $this->setUpQuery();
    }

    /**
     * Test that the Query instance gets passed parameters
     * on a GET request
     *
     * @return void
     */
    public function testQueryMiddlewareWillBeTriggeredOnGetRequest()
    {
        $this->get($this->url, ['content-type' => 'application/vnd.api+json']);
        $this->assertCount(2, $this->getQueryArray());
        $this->assertResponseOk();
    }

    /**
     * Test that the Query instance does not get passed parameters
     * on a POST Request
     *
     * @return void
     */
    public function testQueryMiddlewareWillNotBeTriggeredOnPostRequest()
    {
        $this->json('POST', $this->url, ['data' => ['type' => 'test']], ['content-type' => 'application/vnd.api+json']);

        $this->assertCount(0, $this->getQueryArray());
        $this->assertResponseStatus(201);
    }

    /**
     * Test that the Query instance does not get passed parameters
     * on a PUT Request
     *
     * @return void
     */
    public function testQueryMiddlewareWillNotBeTriggeredOnPutRequest()
    {
        $this->put($this->url, [], ['content-type' => 'application/vnd.api+json']);

        $this->assertCount(0, $this->getQueryArray());
        $this->assertResponseOk();
    }

    /**
     * Test that the Query instance does not get passed parameters
     * on a PATCH Request
     *
     * @return void
     */
    public function testQueryMiddlewareWillNotBeTriggeredOnPatchRequest()
    {
        $this->json('PATCH', $this->url, [
            'data' => [
                'type' => '',
                'id' => 1
            ]
        ], ['content-type' => 'application/vnd.api+json']);

        $this->assertCount(0, $this->getQueryArray());
        $this->assertResponseOk();
    }

    /**
     * Test that the Query instance does not get passed parameters
     * on a DELETE Request
     *
     * @return void
     */
    public function testQueryMiddlewareWillNotBeTriggeredOnDeleteRequest()
    {
        $this->delete($this->url, [], ['content-type' => 'application/vnd.api+json']);

        $this->assertCount(0, $this->getQueryArray());
        $this->assertResponseStatus(204);
    }

    /**
     * Setup the routes for running middleware tests
     *
     * @return void
     */
    protected function setUpRoutes()
    {
        $app = app();
        $router = $app->router;

        $router->group(['middleware' => ['query']], function ($router) {
            $router->get('api-query-middleware', function () {
                return response('api query middleware', 200);
            });

            $router->post('api-query-middleware', function () {
                return response('api query middleware', 200);
            });

            $router->put('api-query-middleware', function () {
                return response('api query middleware', 200);
            });

            $router->patch('api-query-middleware', function () {
                return response('api query middleware', 200);
            });

            $router->delete('api-query-middleware', function () {
                return response('api query middleware', 200);
            });
        });
    }

    /**
     * Create the HTTP url string w/parameters for testing
     *
     * @return void
     */
    protected function setUpUrl()
    {
        $this->url = '/api-query-middleware?' . http_build_query($this->queryString);
    }
}
