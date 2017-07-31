<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\User;

class TenantScopeTest extends TestCase
{
    use Luminary\Services\Testing\BaseTestingTrait;

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

        $this->seed(10, 3, 10);

        TenantModelScope::setTenantId($this->tenantId);
    }

    /**
     * Test that the tenant id is set
     *
     * @return void
     */
    public function testTenantIdSet() :void
    {
        $this->assertEquals(TenantModelScope::getTenantId(), $this->tenantId);
    }

    /**
     * Test that the scope was booted in the model
     *
     * @return void
     */
    public function testModelTraitScoped() :void
    {
        $expected = $this->customers->where('tenant_id', $this->tenantId);
        $results = Customer::all();

        $this->assertCount($expected->count(), $results);
        $this->assertEquals($expected->pluck('id')->all(), $results->pluck('id')->all());

        Customer::clearBootedModels();
    }

    /**
     * Test the Trait remove scope method
     *
     * @return void
     */
    public function testModelWithScopeRemoved() :void
    {
        $expected = $this->customers->count();
        $results = Customer::withoutTenant()->get();

        $this->assertCount($expected, $results);

        Customer::clearBootedModels();
    }

    /**
     * Test the builder macro remove scope
     *
     * @return void
     */
    public function testScopeRemoveTenant() :void
    {
        $expected = $this->customers->count();
        $results = Customer::query()->withoutTenant()->get();

        $this->assertCount($expected, $results);

        Customer::clearBootedModels();
    }

    /**
     * Test scoping by a different tenant id
     *
     * @return void
     */
    public function testScopeByTenant() :void
    {
        $expected = $this->customers->where('tenant_id', 1234);
        $results = Customer::byTenant(1234)->get();

        $this->assertCount($expected->count(), $results);
        $this->assertEquals($expected->pluck('id')->all(), $results->pluck('id')->all());

        Customer::clearBootedModels();
    }

    /**
     * Test scoping by a different tenant id,
     * w/correct relationships included
     *
     * @return void
     */
    public function testScopeByTenantRelationships() :void
    {
        $id = 1234;
        $expected = $this->customers->where('tenant_id', $id)->first();
        $results = Customer::byTenant($id)->with('location', 'users')->get()->first();

        $this->assertEquals($id, $results->location->tenant_id);
        $this->assertEquals($expected->users->pluck('tenant_id')->all(), $results->users->pluck('tenant_id')->all());

        Customer::clearBootedModels();
    }

    /**
     * Test scoping by a different tenant id,
     * w/correct relationships lazy loaded
     *
     * @return void
     */
    public function testScopeByTenantRelationshipsWithLazyLoad() :void
    {
        $id = 1234;
        $expected = $this->customers->where('tenant_id', $id)->first();
        $results = Customer::byTenant($id)->get()->first();
        $results->load('location', 'users');

        $this->assertEquals($id, $results->location->tenant_id);
        $this->assertEquals($expected->users->pluck('tenant_id')->all(), $results->users->pluck('tenant_id')->all());

        Customer::clearBootedModels();
    }

    /**
     * Test scoping by multiple tenants
     *
     * @return void
     */
    public function testScopeByTenantArray() :void
    {
        $expected = $this->customers;
        $results = Customer::byTenant([1234, 2345])->get();

        $this->assertCount($expected->count(), $results);
        $this->assertEquals($expected->pluck('id')->all(), $results->pluck('id')->all());

        Customer::clearBootedModels();
    }

    /**
     * Test global override of tenant scope
     *
     * @return void
     */
    public function testScopeOverride() :void
    {
        TenantModelScope::setOverride();

        $expected = $this->customers;
        $results = Customer::all();

        $this->assertCount($expected->count(), $results);
        $this->assertEquals($expected->pluck('id')->all(), $results->pluck('id')->all());

        TenantModelScope::setOverride(false);
        Customer::clearBootedModels();
    }
}
