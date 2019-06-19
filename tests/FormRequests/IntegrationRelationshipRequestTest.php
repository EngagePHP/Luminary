<?php

use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Authorizers\CustomerAuthorize;
use Luminary\Services\Testing\BaseTestingTrait;

class IntegrationRelationshipRequestTest extends TestCase
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

        $this->router->get('/customers/{id}/relationships/{relationship}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Index $request, $id, $relationship) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $this->get('/customers/1234/relationships/location');

        $this->assertResponseOk();
        $this->assertInstanceOf(\Luminary\Http\Requests\Index::class, $requestInstance);
        $this->assertInstanceOf(CustomerAuthorize::class, $requestInstance->getAuthorize());
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

        $this->router->post('/customers/{id}/relationships/{relationship}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Store $request, $id, $relationship) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $data = [
            'data' => [
                'type' => 'location',
                'id' => '1234',
            ]
        ];

        $this->json('POST', 'customers/1234/relationships/location', $data , $this->headers());

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Store::class, $requestInstance);
        $this->assertInstanceOf(CustomerAuthorize::class, $requestInstance->getAuthorize());
        $this->assertInstanceOf(\Luminary\Http\Requests\Store::class, $requestInstance->validatorArgs());
        $this->assertInstanceOf(\Luminary\Services\Sanitation\DefaultSanitizable::class, $requestInstance->getSanitizable());
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

        $this->router->delete('/customers/{id}/relationships/{relationship}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Destroy $request, $id, $relationship) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $data = [
            'data' => [
                'type' => 'location',
                'id' => '1234'
            ]
        ];

        $this->json('DELETE', 'customers/1234/relationships/location', $data, $this->headers());

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Destroy::class, $requestInstance);
        $this->assertInstanceOf(CustomerAuthorize::class, $requestInstance->getAuthorize());
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

        $this->router->patch('/customers/{id}/relationships/{relationship}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Update $request) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $data = [
            'data' => [
                'type' => 'location',
                'id' => '1234'
            ]
        ];

        $this->json('PATCH', 'customers/1234/relationships/location', $data , $this->headers());

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Update::class, $requestInstance);
        $this->assertInstanceOf(CustomerAuthorize::class, $requestInstance->getAuthorize());
        $this->assertInstanceOf(\Luminary\Http\Requests\Update::class, $requestInstance->validatorArgs());
        $this->assertInstanceOf(\Luminary\Services\Sanitation\DefaultSanitizable::class, $requestInstance->getSanitizable());
    }
}
