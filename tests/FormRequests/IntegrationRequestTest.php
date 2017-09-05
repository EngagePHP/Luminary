<?php

use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Authorizers\CustomerAuthorize;
use Luminary\Services\Testing\BaseTestingTrait;

class IntegrationRequestTest extends TestCase
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
    public function setUp()
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

        $this->router->get('/customers', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Index $request) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $this->get('/customers');

        $this->assertResponseOk();
        $this->assertInstanceOf(\Luminary\Http\Requests\Index::class, $requestInstance);
        $this->assertInstanceOf(CustomerAuthorize::class, $requestInstance->getAuthorize());
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

        $this->router->get('/customers', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Show $request) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $this->get('/customers');

        $this->assertResponseOk();
        $this->assertInstanceOf(\Luminary\Http\Requests\Show::class, $requestInstance);
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

        $this->router->post('/customers', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Store $request) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $data = [
            'data' => [
                'type' => 'customers',
                'attributes' => [],
            ]
        ];

        $this->json('POST', 'customers', $data , $this->headers());

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Store::class, $requestInstance);
        $this->assertInstanceOf(CustomerAuthorize::class, $requestInstance->getAuthorize());
        $this->assertInstanceOf(\Luminary\Services\Testing\Validators\CustomerCreate::class, $requestInstance->validatorArgs());
        $this->assertInstanceOf(\Luminary\Services\Testing\Sanitizers\CustomerSanitizer::class, $requestInstance->getSanitizable());
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

        $this->router->delete('/customers/{id}', [
            'middleware' => ['request', 'response'],
            function(\Luminary\Http\Requests\Destroy $request) use(&$requestInstance) {
                $requestInstance = $request;
            }
        ]);

        $this->json('DELETE', 'customers/1234', [], $this->headers());

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

        $this->router->patch('/customers/{id}', [
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

        $this->json('PATCH', 'customers/1234', $data , $this->headers());

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Update::class, $requestInstance);
        $this->assertInstanceOf(CustomerAuthorize::class, $requestInstance->getAuthorize());
        $this->assertInstanceOf(\Luminary\Services\Testing\Validators\CustomerUpdate::class, $requestInstance->validatorArgs());
        $this->assertInstanceOf(\Luminary\Services\Testing\Sanitizers\CustomerSanitizer::class, $requestInstance->getSanitizable());
    }
}
