<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Luminary\Database\Eloquent\Relations\ManageRelations;
use Luminary\Database\Eloquent\Relations\RelationshipNotFoundException;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\Location;
use Luminary\Services\Testing\Models\User;

class ManageRelationsTraitTest extends TestCase
{
    use DatabaseMigrations;

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
        $this->trait = new class {
            use ManageRelations;
        };

        parent::setUp();
        TenantModelScope::setOverride();
    }

    /**
     * Test the retrieval of a model relationship by name
     *
     * @return void
     */
    public function testGetModelRelationshipMethod() :void
    {
        $model = new Customer;
        $relationship = 'location';

        $relation = $this->trait->getModelRelationship($relationship, $model);

        $this->assertInstanceOf(Relation::class, $relation);
    }

    /**
     * Test the exception for a relationship that doesn't exist
     *
     * @return void
     */
    public function testGetModelRelationshipMethodException() :void
    {
        $this->expectException(RelationshipNotFoundException::class);
        $this->expectExceptionMessage('The notavailable relationship was not found');

        $model = new Customer;
        $relationship = 'notavailable';

        $this->trait->getModelRelationship($relationship, $model);
    }

    /**
     * Test the get relation method based on the relation class
     *
     * @return void
     */
    public function testGetRelationMethod() :void
    {
        $relation = (new Customer)->location();
        $method = $this->trait->getRelationMethod($relation, 'create');

        $this->assertEquals('associate', $method);
    }

    /**
     * Test the get related models method with a has one relation
     *
     * @return void
     */
    public function testHasOneGetRelatedModelsMethod() :void
    {
        $customer = new Customer;

        $location = factory(Location::class, 1)->create()->first();
        $locationRelation = $this->trait->getModelRelationship('location', $customer);
        $locationRelated = $this->trait->getRelatedModels($locationRelation, $location->id);

        $this->assertEquals($location->toArray(), $locationRelated->toArray());
    }

    /**
     * Test the get related models method with a has many relation
     *
     * @return void
     */
    public function testHasManyGetRelatedModelsMethod() :void
    {
        $customer = new Customer;

        $users = factory(User::class, 3)->create();
        $ids = $users->pluck('id');
        $users = User::find($ids);
        $usersRelation = $this->trait->getModelRelationship('users', $customer);
        $usersRelated = $this->trait->getRelatedModels($usersRelation, $ids);

        $this->assertEquals($users->toArray(), array_map(
            function($user) {
                return $user->toArray();
            },
            $usersRelated
        ));
    }

    /**
     * Test the manage relationship trait method with a has one relation
     *
     * @return void
     */
    public function testHasOneManageRelationshipMethod() :void
    {
        $customer = factory(Customer::class, 1)->create()->first();
        $location = factory(Location::class, 1)->create()->first();

        $this->trait->createRelationship($customer, 'location', $location->id);

        $this->assertInstanceOf(Location::class, $customer->getRelation('location'));
    }

    /**
     * Test the manage relationship trait method with a has many relation
     *
     * @return void
     */
    public function testHasManyManageRelationshipMethod() :void
    {
        $customer = factory(Customer::class, 1)->create()->first();
        $users = factory(User::class, 3)->create();
        $ids = $users->pluck('id');
        $this->trait->createRelationship($customer, 'users', $ids);

        $relation = $customer->getRelation('users');

        $this->assertInstanceOf(Collection::class, $relation);
        $this->assertEquals($ids, $relation->pluck('id'));
    }

    /**
     * Test the manage relationships trait method
     *
     * @return void
     */
    public function testManageRelationships() :void
    {
        $customer = factory(Customer::class, 1)->create()->first();
        $location = factory(Location::class, 1)->create()->first();
        $users = factory(User::class, 3)->create();

        $this->trait->createRelationships($customer, [
            'location' => $location->id,
            'users' => $users->pluck('id')
        ]);

        $expected = [
            'location' => $location->id,
            'users' => $users->pluck('id')->all()
        ];

        $results = $customer->getRelations();
        $results = collect($results)->map(
            function($relation) {
                return $relation instanceof Model ? $relation->id : $relation->pluck('id');
            }
        )->toArray();

        $this->assertEquals($expected, $results);
    }
}
