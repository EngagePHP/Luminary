<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Luminary\Services\Testing\BaseTestingTrait;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Repositories\CustomerRepository;

class GeneratedRepositoriesTest extends TestCase
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
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(5, 5, 5);
    }

    /**
     * Test the All method returns
     * all of the seeded customers
     *
     * @return void
     */
    public function testAllMethod() :void
    {
        $all = CustomerRepository::all();

        $this->assertCount(5, $all);
    }

    /**
     * Test that the find method returns
     * a single model instance
     * when found
     *
     * @return void
     */
    public function testFindMethod() :void
    {
        $id = $this->customers->first()->id;
        $find = CustomerRepository::find($id);

        $this->assertInstanceOf(Customer::class, $find);
    }

    /**
     * Test that the find all method returns a collection
     *
     * @return void
     */
    public function testFindAllMethod() :void
    {
        $ids = $this->customers->pluck('id')->all();
        $find = CustomerRepository::findAll($ids);

        $this->assertInstanceOf(Collection::class, $find);
        $this->assertCount(5, $find);
    }

    /**
     * Test that the create method without
     * relationships returns a created model
     * without relationships
     *
     * @return void
     */
    public function testCreateMethod() :void
    {
        $data = factory(Customer::class, 1)->make()->first()->toArray();
        $create = CustomerRepository::create($data);
        $relations = $create->getRelations();

        $this->assertInstanceOf(Customer::class, $create);
        $this->assertArrayHasKey('id', $create->toArray());
        $this->assertEmpty($relations);
    }

    /**
     * Test that the create method with relationships
     * passed along returns the model with the correct
     * relationships
     *
     * @return void
     */
    public function testCreateMethodWithRelationships() :void
    {
        $data = factory(Customer::class, 1)->make()->first()->toArray();
        $location = $this->locations->first()->id;
        $users = $this->users->take(3)->pluck('id')->all();
        $relationships = compact('location', 'users');

        $customer = CustomerRepository::create($data, $relationships);

        $expected = $relationships;

        $results = collect($customer->getRelations())->map(
            function($relation) {
                return $relation instanceof Model ? $relation->id : $relation->pluck('id');
            }
        )->toArray();

        $this->assertEquals($expected, $results);
    }

    /**
     * Test that the create method does not
     * return false when inserting multiple
     * records
     *
     * @return void
     */
    public function testCreateAllMethod() :void
    {
        $data = factory(Customer::class, 2)->make()->toArray();
        $customers = CustomerRepository::createAll($data);

        $this->assertTrue((bool) $customers);
    }

    /**
     * Test that the update method without relationships
     * returns the updated model without relationships
     *
     * @return void
     */
    public function testUpdateMethod()
    {
        $data = factory(Customer::class, 1)->make()->first()->toArray();
        $original = $this->customers->first();

        $original->setRelations([]);

        $id = $original->id;
        $expected = array_merge($original->toArray(), $data);

        $customer = CustomerRepository::update($id, $data);
        $relations = $customer->getRelations();

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals($expected, $customer->toArray());
        $this->assertEmpty($relations);
    }

    /**
     * Test that the create method with relationships
     * passed along returns the model with the correct
     * relationships
     *
     * @return void
     */
    public function testUpdateMethodWithRelationships() :void
    {
        $data = factory(Customer::class, 1)->make()->first()->toArray();

        $original = $this->customers->first();
        $originalRelations = $original->load('location', 'users')->getRelations();
        $original->setRelations([]);

        $id = $original->id;
        $location = $this->locations->random()->id;
        $users = $this->users->random(3)->pluck('id')->all();
        $relationships = compact('location', 'users');

        $customer = CustomerRepository::update($id, $data, $relationships);
        $customerExpected = array_merge($original->toArray(), $data, ['location_id' => $location]);

        // Assert Model
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals($customerExpected, array_except($customer->toArray(), ['location', 'users']));

        //Assert Location Relationship
        $this->assertEquals($originalRelations['location']->id, $original->location_id);
        $this->assertEquals($customer->getRelation('location')->id, $location);

        // Assert Users Relationship
        $usersExpected = $originalRelations['users']->pluck('id')->merge($users)->sort()->unique()->values();
        $usersResults = $customer->getRelation('users')->pluck('id')->sort()->values();

        $this->assertEquals($usersExpected, $usersResults);
    }

    /**
     * Test that the create method does not
     * return false when inserting multiple
     * records
     *
     * @return void
     */
    public function testUpdateAllMethod() :void
    {
        $data = factory(Customer::class, 1)->make()->first()->toArray();
        $keys = array_keys($data);
        $customers = $this->customers->random(2)->pluck('id')->all();

        $update = CustomerRepository::updateAll($customers, $data);
        $updated = CustomerRepository::findAll($customers);

        $this->assertTrue((bool) $update);

        $updated->each(
            function($customer) use ($keys, $data){
                $updated = array_only($customer->getAttributes(), $keys);
                $this->assertEquals($data, $updated);
            }
        );
    }

    /**
     * Test that we can delete models
     *
     * @return void
     */
    public function testDeleteMethod() :void
    {
        $id = $this->customers->first()->id;
        $delete = CustomerRepository::delete($id);
        $alreadyDeleted = CustomerRepository::delete($id);

        $this->assertTrue($delete);
        $this->assertFalse($alreadyDeleted);
    }

    /**
     * Test that we can delete multiple models
     *
     * @return void
     */
    public function testDeleteAllMethod() :void
    {
        $ids = $this->customers->random(3)->pluck('id')->all();
        $deleted = CustomerRepository::deleteAll($ids);

        $this->assertTrue($deleted);
        $this->assertEmpty(CustomerRepository::findAll($ids)->toArray());
    }
}
