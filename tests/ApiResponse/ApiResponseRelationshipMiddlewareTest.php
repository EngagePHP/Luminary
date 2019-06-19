<?php

use Illuminate\Http\Request;
use Luminary\Services\ApiResponse\ResponseMiddleware;

class ApiResponseRelationshipMiddlewareTest extends TestCase
{
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
    protected function setUp(): void
    {
        parent::setUp();

        $router = app()->router;
        $router->group(['middleware' => 'response'], function () use ($router) {
            $router->get('test/{id}', function() {
                return;
            });

            $router->get('test/{id}/relationships/{relationship}', function() {
                return;
            });

            $router->get('test/{id}/{relationship}', function() {
                return;
            });
        });
    }

    /**
     * Test that the relationship response is not triggered
     *
     * @return void
     */
    public function testRelationshipResponseNotTriggered() :void
    {
        $this->json('get', '/test/12345', $this->headers);

        $this->assertFalse(ResponseMiddleware::$relationshipResponse);
    }

    /**
     * Test that the self link for relationship triggers the
     * relationship response
     *
     * @return void
     */
    public function testRelationshipResponseTriggeredForSelfLink() :void
    {
        $this->json('get', '/test/12345/relationships/users', $this->headers);

        $this->assertTrue(ResponseMiddleware::$relationshipResponse);

        ResponseMiddleware::$relationshipResponse = false;
    }

    /**
     * Test that the related link for a relationship triggers the
     * relationship response
     *
     * @return void
     */
    public function testRelationshipResponseTriggeredForRelatedLink() :void
    {
        $this->json('get', '/test/12345/users', $this->headers);

        $this->assertTrue(ResponseMiddleware::$relationshipResponse);

        ResponseMiddleware::$relationshipResponse = false;
    }
}
