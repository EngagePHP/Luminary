<?php

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
    public function setUp()
    {
        parent::setUp();

        $app = app();
        $app->group(['middleware' => 'response'], function () use ($app) {
            $app->get('customers/{id}', function() {
                return;
            });

            $app->get('customers/{id}/relationships/{relationship}', function() {
                return;
            });

            $app->get('customers/{id}/{relationship}', function() {
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
        $this->json('get', '/customers/12345', $this->headers);

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
        $this->json('get', '/customers/12345/relationships/users', $this->headers);

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
        $this->json('get', '/customers/12345/users', $this->headers);

        $this->assertTrue(ResponseMiddleware::$relationshipResponse);

        ResponseMiddleware::$relationshipResponse = false;
    }
}
