<?php

class QueryMiddlewareTest extends TestCase
{
    use Luminary\Services\ApiQuery\Testing\BaseQueryTrait;

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
    public function setUp()
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

        $this->assertCount(1, $this->getQueryArray());
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
        $this->post($this->url, [], ['content-type' => 'application/vnd.api+json']);
        $response = $this->response;

        $this->assertCount(0, $this->getQueryArray());
        $this->assertEquals(200, $response->status());
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
        $response = $this->response;

        $this->assertCount(0, $this->getQueryArray());
        $this->assertEquals(200, $response->status());
    }

    /**
     * Test that the Query instance does not get passed parameters
     * on a PATCH Request
     *
     * @return void
     */
    public function testQueryMiddlewareWillNotBeTriggeredOnPatchRequest()
    {
        $this->patch($this->url, [], ['content-type' => 'application/vnd.api+json']);
        $response = $this->response;

        $this->assertCount(0, $this->getQueryArray());
        $this->assertEquals(200, $response->status());
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
        $response = $this->response;

        $this->assertCount(0, $this->getQueryArray());
        $this->assertEquals(200, $response->status());
    }
}
