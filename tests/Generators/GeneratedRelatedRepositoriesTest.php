<?php

use Illuminate\Database\Eloquent\Collection;
use Luminary\Database\Eloquent\Model;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\BaseTestingTrait;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\Location;
use Luminary\Services\Testing\Models\User;
use Luminary\Services\Testing\Repositories\CustomerRelatedRepository;

class GeneratedRelatedRepositoriesTest extends TestCase
{
    use BaseTestingTrait;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(20, 5, 5);
        TenantModelScope::setOverride();
    }

    /**
     * Test the All method returns
     * all of the seeded customers
     *
     * @return void
     */
    public function testAllMethod() :void
    {
        $customer = $this->customers->first();
        $count = $customer->users()->get(['id'])->count();

        $all = CustomerRelatedRepository::all($customer->id, 'users');

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertCount($count, $all);
    }

    /**
     * Test the Find method returns
     * a single relationship
     *
     * @return void
     */
    public function testFindMethod() :void
    {
        $customer = $this->customers->first();
        $expected = $customer->getRelation('location')->toArray();

        $location = CustomerRelatedRepository::find($customer->id, 'location');

        $this->assertInstanceOf(Model::class, $location);
        $this->assertEquals($expected, $location->toArray());
    }

    /**
     * Test the create method for a HasOne
     * relationship creates the method and
     * attaches it to the parent
     *
     * @return void
     */
    public function testHasOneCreateMethod() :void
    {
        $customer = $this->customers->first();
        $originalLocation = $customer->getRelation('location');
        $data = factory(Location::class)->make()->toArray();
        $newLocation = CustomerRelatedRepository::create($customer->id, 'location', $data);
        $locationIdFromQuery = Customer::with('location')->whereId($customer->id)->first()->getRelation('location')->id;

        $this->assertNotEquals($originalLocation->id, $newLocation->id);
        $this->assertEquals($locationIdFromQuery, $newLocation->id);
        $this->assertEquals($this->locations->count() + 1, $newLocation->id);
    }

    /**
     * Test the create method for a HasOne
     * relationship creates the method and
     * attaches it to the parent
     *
     * @return void
     */
    public function testHasManyCreateMethod() :void
    {
        $customer = $this->customers->first();
        $originalUsers = $customer->users()->get(['id'])->pluck('id')->all();
        $data = factory(User::class)->make()->toArray();
        $newUser = CustomerRelatedRepository::create($customer->id, 'users', $data)->id;
        $userIdsFromQuery = Customer::with('users')->whereId($customer->id)->first()->getRelation('users')->pluck('id')->all();

        $this->assertTrue(in_array($newUser, $userIdsFromQuery));
        $this->assertEquals(count($originalUsers) + 1, count($userIdsFromQuery));
    }

    /**
     * Test the update method for the related repository
     *
     * @return void
     */
    public function testUpdateMethod() :void
    {
        $customer = $this->customers->first();
        $user = $customer->users()->first();
        $update = CustomerRelatedRepository::update($customer->id, 'users', $user->id, ['first_name' => 'UpdatedNameForTest']);
        $findByQuery = CustomerRelatedRepository::find($customer->id, 'users', $user->id);

        $this->assertNotEquals($user->first_name, $update->first_name);
        $this->assertEquals('UpdatedNameForTest', $findByQuery->first_name);
    }

    /**
     * Test the deleted method for the related repository
     *
     * @return void
     */
    public function testDeleteMethod() :void
    {
        $customer = $this->customers->first();
        $user = $customer->users()->first();
        $delete = CustomerRelatedRepository::delete($customer->id, 'users', $user->id, ['first_name' => 'UpdatedNameForTest']);
        $findByQuery = CustomerRelatedRepository::find($customer->id, 'users', $user->id);

        $this->assertEmpty($findByQuery->toArray());
    }
}
