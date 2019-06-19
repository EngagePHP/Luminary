<?php

use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\BaseTestingTrait;
use Luminary\Services\Testing\Models\User;
use Luminary\Services\Testing\Policies\CustomerPolicy;
use Luminary\Services\Testing\Policies\LocationPolicy;

class GeneratedPolicyTest extends TestCase
{
    use BaseTestingTrait;

    /**
     * Instance of a class with
     * manage relationships trait
     *
     * @var \Luminary\Database\Eloquent\Relations\ManageRelations
     */
    protected $trait;

    /**
     * The User Instance
     *
     * @var User
     */
    protected $user;

    /**
     * The Admin User Instance
     *
     * @var User
     */
    protected $admin;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(5,5,5);

        TenantModelScope::setOverride();
    }

    /**
     * Test the user permissions
     *
     * @return void
     */
    public function testUserPermissionsMethod() :void
    {
        $user = $this->users->random();
        $user->assignRole('user');

        $policy = (new LocationPolicy);

        $this->assertTrue($policy->view($user));
        $this->assertFalse($policy->create($user));
        $this->assertFalse($policy->update($user));
        $this->assertFalse($policy->delete($user));

        $policy = (new CustomerPolicy);
        $this->assertFalse($policy->view($user));
        $this->assertFalse($policy->create($user));
        $this->assertFalse($policy->update($user));
        $this->assertFalse($policy->delete($user));
    }

    /**
     * Test the admin permissions
     *
     * @return void
     */
    public function testAdminPermissionsMethod() :void
    {
        $user = $this->users->random();
        $user->assignRole('admin');

        $policy = (new LocationPolicy);

        $this->assertTrue($policy->view($user));
        $this->assertTrue($policy->create($user));
        $this->assertTrue($policy->update($user));
        $this->assertTrue($policy->delete($user));

        $policy = (new CustomerPolicy);
        $this->assertTrue($policy->view($user));
        $this->assertTrue($policy->create($user));
        $this->assertTrue($policy->update($user));
        $this->assertTrue($policy->delete($user));
    }
}
