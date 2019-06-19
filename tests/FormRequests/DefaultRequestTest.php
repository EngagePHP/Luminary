<?php

use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\BaseTestingTrait;

class DefaultRequestTest extends TestCase
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
     * Test that the an index request returns
     * and ok status
     *
     * @return void
     */
    public function testIndex()
    {
        $requestInstance = null;

        $this->router->get('/indexTest', function(\Luminary\Http\Requests\Index $request) use(&$requestInstance) {
            $requestInstance = $request;
        });

        $this->get('/indexTest');

        $this->assertResponseOk();
        $this->assertInstanceOf(\Luminary\Http\Requests\Index::class, $requestInstance);
        $this->assertInstanceOf(\Luminary\Services\Auth\Authorize::class, $requestInstance->getAuthorize());

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

        $this->router->get('/showTest', function(\Luminary\Http\Requests\Show $request) use(&$requestInstance) {
            $requestInstance = $request;
        });

        $this->get('/showTest');

        $this->assertResponseOk();
        $this->assertInstanceOf(\Luminary\Http\Requests\Show::class, $requestInstance);
        $this->assertInstanceOf(\Luminary\Services\Auth\Authorize::class, $requestInstance->getAuthorize());
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

        $this->router->post('/storeTest', function(\Luminary\Http\Requests\Store $request) use(&$requestInstance) {
            $requestInstance = $request;
        });

        $this->post('/storeTest');

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Store::class, $requestInstance);
        $this->assertInstanceOf(\Luminary\Services\Auth\Authorize::class, $requestInstance->getAuthorize());
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

        $this->router->delete('/deleteTest', function(\Luminary\Http\Requests\Destroy $request) use(&$requestInstance) {
            $requestInstance = $request;
        });

        $this->delete('/deleteTest');

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Destroy::class, $requestInstance);
        $this->assertInstanceOf(\Luminary\Services\Auth\Authorize::class, $requestInstance->getAuthorize());
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

        $this->router->post('/updateTest', function(\Luminary\Http\Requests\Update $request) use(&$requestInstance) {
            $requestInstance = $request;
        });

        $this->post('/updateTest');

        $this->assertResponseStatus(204);
        $this->assertInstanceOf(\Luminary\Http\Requests\Update::class, $requestInstance);
        $this->assertInstanceOf(\Luminary\Services\Auth\Authorize::class, $requestInstance->getAuthorize());
        $this->assertInstanceOf(\Luminary\Http\Requests\Update::class, $requestInstance->validatorArgs());
        $this->assertInstanceOf(\Luminary\Services\Sanitation\DefaultSanitizable::class, $requestInstance->getSanitizable());
    }
}
