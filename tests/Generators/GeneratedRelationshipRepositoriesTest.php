<?php

use Illuminate\Database\Eloquent\Collection;
use Luminary\Database\Eloquent\Model;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\BaseTestingTrait;
use Luminary\Services\Testing\Repositories\CustomerRelationshipRepository;

class GeneratedRelationshipsRepositoriesTest extends TestCase
{
    use BaseTestingTrait;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(5, 5, 5);
        TenantModelScope::setOverride();
    }

    /**
     * Test the All method returns
     * all of the seeded customers
     *
     * @return void
     */
    public function testAllHasManyMethod() :void
    {
        $customer = $this->customers->first();
        $users = $customer->users()->get(['id']);

        $all = CustomerRelationshipRepository::all($customer->id, 'users');

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertCount($users->count(), $all);
        $this->assertEquals($users->toArray(), $all->toArray());
    }

    /**
     * Test the All method returns
     * all of the seeded customers
     *
     * @return void
     */
    public function testAllManyToManyMethod() :void
    {
        $customer = $this->customers->first();
        $interests = $customer->interests()->get(['interests.id']);

        $all = CustomerRelationshipRepository::all($customer->id, 'interests');

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertCount($interests->count(), $all);
        $this->assertEquals($interests->toArray(), $all->toArray());
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

        $location = CustomerRelationshipRepository::find($customer->id, 'location');

        $this->assertInstanceOf(Model::class, $location);
        $this->assertEquals(array_only($expected, ['id']), $location->toArray());
    }

    /**
     * Test the create method for a HasMany
     * relationship attaches the object to the
     * parent
     *
     * @return void
     */
    public function testHasManyCreateMethod() :void
    {
        $customer = $this->customers->first();
        $originalUsers = $customer->users()->get(['id'])->pluck('id');
        $ids = $this->users->random(3)->pluck('id')->all();
        $expected = $originalUsers->merge($ids)->sort()->unique()->values()->all();

        $create = CustomerRelationshipRepository::create($customer->id, 'users', $ids);
        $updatedUsers = $customer->users()->get(['id'])->pluck('id')->sort()->values()->all();

        $this->assertTrue($create);
        $this->assertEquals($expected, $updatedUsers);
    }


    /**
     * Test the update method for the related repository
     *
     * @return void
     */
    public function testHasOneUpdateMethod() :void
    {
        $customer = $this->customers->random()->first();
        $location = $this->locations->random()->first()->id;

        $update = CustomerRelationshipRepository::update($customer->id, 'location', $location);

        $expected = CustomerRelationshipRepository::find($customer->id, 'location');

        $this->assertTrue($update);
        $this->assertEquals($expected->id, $location);
    }

    /**
     * Test the update method for the related repository
     *
     * @return void
     */
    public function testHasManyUpdateMethod() :void
    {
        $customer = $this->customers->first();
        $originalUsers = $customer->users()->get(['id'])->pluck('id');
        $ids = $originalUsers->random(3)->sort()->values()->all();

        $update = CustomerRelationshipRepository::update($customer->id, 'users', $ids);
        $updatedUsers = $customer->users()->get(['id'])->pluck('id')->sort()->values()->all();

        $this->assertTrue($update);
        $this->assertEquals($ids, $updatedUsers);
    }

    /**
     * Test the update method for the related repository
     *
     * @return void
     */
    public function testManyToManyUpdateMethod() :void
    {
        $customer = $this->customers->first();
        $originalInterests = $customer->interests()->get(['interests.id'])->pluck('id');
        $ids = $originalInterests->random(3)->sort()->values()->all();

        $update = CustomerRelationshipRepository::update($customer->id, 'interests', $ids);
        $updatedInterests = $customer->interests()->get(['interests.id'])->pluck('id')->sort()->values()->all();

        $this->assertTrue($update);
        $this->assertEquals($ids, $updatedInterests);
    }

    /**
     * Test the deleted method for the related repository
     *
     * @return void
     */
    public function testHasOneDeleteMethod() :void
    {
        $customer = $this->customers->random()->first();

        $update = CustomerRelationshipRepository::delete($customer->id, 'location');

        $expected = CustomerRelationshipRepository::find($customer->id, 'location');

        $this->assertTrue($update);
        $this->assertNull($expected->id);
    }

    /**
     * Test the deleted method for the related repository
     *
     * @return void
     */
    public function testHasManyDeleteMethod() :void
    {
        $customer = $this->customers->first();
        $originalUsers = $customer->users()->get(['id'])->pluck('id');
        $ids = $originalUsers->random(3)->sort()->values()->all();
        $expected = collect(array_diff($originalUsers->all(), $ids))->sort()->values()->all();

        $update = CustomerRelationshipRepository::delete($customer->id, 'users', $ids);
        $updatedUsers = $customer->users()->get(['id'])->pluck('id')->sort()->values()->all();

        $this->assertTrue($update);
        $this->assertEquals($expected, $updatedUsers);
    }

    /**
     * Test the deleted method for the related repository
     *
     * @return void
     */
    public function testManyToManyDeleteMethod() :void
    {
        $customer = $this->customers->first();
        $originalInterests = $customer->interests()->get(['interests.id'])->pluck('id');
        $ids = $originalInterests->random(3)->sort()->values()->all();
        $expected = collect(array_diff($originalInterests->all(), $ids))->sort()->values()->all();

        $update = CustomerRelationshipRepository::delete($customer->id, 'interests', $ids);
        $updatedInterests = $customer->interests()->get(['interests.id'])->pluck('id')->sort()->values()->all();

        $this->assertTrue($update);
        $this->assertEquals($expected, $updatedInterests);
    }
}
