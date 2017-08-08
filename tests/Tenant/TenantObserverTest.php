<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\Customer;

class TenantObserverTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * The Tenant Id
     *
     * @var int
     */
    protected $tenantId = 2345;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        TenantModelScope::setOverride(false);
        TenantModelScope::setTenantId($this->tenantId);
    }

    /**
     * Test that the tenant id is set
     *
     * @return void
     */
    public function testCreate() :void
    {
        $customer = factory(Customer::class)->create(['tenant_id' => $this->tenantId]);

        $this->assertNotNull($customer->id);
        $this->assertEquals($this->tenantId, $customer->tenant_id);
    }

    /**
     * Test that the tenant id is set
     *
     * @return void
     */
    public function testUpdate() :void
    {
        $customer = factory(Customer::class)->create();

        TenantModelScope::setTenantId(1234);

        $customer->update();

        $this->assertEquals(1234, $customer->tenant_id);
    }
}
