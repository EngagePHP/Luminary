<?php

use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Authorizers\LocationAuthorize;
use Luminary\Services\Testing\BaseTestingTrait;

class IntegrationRelatedRequestTest extends TestCase
{
    use BaseTestingTrait;

    /**
     * The application router instance
     *
     * @var object
     */

    protected $router;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->router = app()->router;

        TenantModelScope::setOverride();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        (new \Luminary\Services\Testing\ServiceProvider(app()))->loadRoutes();

        return app();
    }

    /**
     * Test that the an index request returns
     * and ok status
     *
     * @return void
     */
    public function testIndex()
    {
        $requestInstance = null;

        $this->router->get('/customers/{id}/{related}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Index $request, $id, $related) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $this->get('/customers/1234/location');

        $this->assertResponseOk();
        $this->assertInstanceOf(\Luminary\Http\Requests\Index::class, $requestInstance);
        $this->assertInstanceOf(LocationAuthorize::class, $requestInstance->getAuthorize());
    }

    /**
     * Test that a show request returns
     * correct status
     *
     * @return void
     */
    public function testShow()
    {
        $requestInstance = null;

        $this->router->get('/customers/{id}/{related}/{relatedId}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Show $request, $id, $related, $relatedId) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $this->get('/customers/1234/location/1234');

        $this->assertResponseOk();
        $this->assertInstanceOf(\Luminary\Http\Requests\Show::class, $requestInstance);
        $this->assertInstanceOf(LocationAuthorize::class, $requestInstance->getAuthorize());
    }

    /**
     * Test that the store request returns
     * the correct status
     *
     * @return void
     */
    public function testStore()
    {
        $requestInstance = null;

        $this->router->post('/customers/{id}/{related}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Store $request, $id, $related) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $data = [
            'data' => [
                'type' => 'customers',
                'attributes' => [],
            ]
        ];

        $this->json('POST', 'customers/1234/location', $data , $this->headers());

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Store::class, $requestInstance);
        $this->assertInstanceOf(LocationAuthorize::class, $requestInstance->getAuthorize());
        $this->assertInstanceOf(\Luminary\Services\Testing\Validators\LocationCreate::class, $requestInstance->validatorArgs());
        $this->assertInstanceOf(\Luminary\Services\Testing\Sanitizers\LocationSanitizer::class, $requestInstance->getSanitizable());
    }

    /**
     * Test the delete request returns
     * the correct status
     *
     * @return void
     */
    public function testDeleteRequest()
    {
        $requestInstance = null;

        $this->router->delete('/customers/{id}/{related}/{relatedId}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Destroy $request, $id, $related, $relatedId) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $this->json('DELETE', 'customers/1234/location/1234', [], $this->headers());

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Destroy::class, $requestInstance);
        $this->assertInstanceOf(LocationAuthorize::class, $requestInstance->getAuthorize());
    }

    /**
     * Test the update request returns the
     * correct status
     *
     * @return void
     */
    public function testUpdate()
    {
        $requestInstance = null;

        $this->router->patch('/customers/{id}/{related}/{relatedId}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Update $request) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $data = [
            'data' => [
                'type' => 'customers',
                'id' => '1234',
                'attributes' => [],
            ]
        ];

        $this->json('PATCH', 'customers/1234/locations/1234', $data , $this->headers());

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Update::class, $requestInstance);
        $this->assertInstanceOf(LocationAuthorize::class, $requestInstance->getAuthorize());
        $this->assertInstanceOf(\Luminary\Services\Testing\Validators\LocationUpdate::class, $requestInstance->validatorArgs());
        $this->assertInstanceOf(\Luminary\Services\Testing\Sanitizers\LocationSanitizer::class, $requestInstance->getSanitizable());
    }
}
