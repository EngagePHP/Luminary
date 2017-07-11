<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Luminary\Services\ApiQuery\Testing\Models\Customer;
use Luminary\Services\ApiQuery\Testing\Models\Location;
use Luminary\Services\ApiQuery\Testing\Models\User;

class QueryModelScopeTest extends TestCase
{
    use DatabaseMigrations;
    use Luminary\Services\ApiQuery\Testing\BaseQueryTrait;

    /**
     * The customer collection
     *
     * @return \Illuminate\Support\Collection
     */
    protected $customers;

    /**
     * The user collection
     *
     * @return \Illuminate\Support\Collection
     */
    protected $users;

    /**
     * The location collection
     *
     * @return \Illuminate\Support\Collection
     */
    protected $locations;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed();
        $this->setUpQuery();
    }

    /**
     * Test the include query
     *
     * @return void
     */
    public function testModelEagerLoading()
    {
        $query = [
            'resource' => 'customers',
            'include' => 'users,users.location'
        ];

        $this->query->setQuery($query)->activate();

        Customer::all()->each(
            function($customer) {
                $users = $customer->getRelation('users');
                $locations = $users->map(function($user) {
                    return $user->getRelation('location');
                });

                $this->assertEquals(3, $users->count());
                $this->assertEquals(3, $locations->count());
            }
        );

        Customer::clearBootedModels();
    }

    /**
     * Test the Fields Query
     *
     * @return void
     */
    public function testModelFields()
    {
        $query = [
            'resource' => 'customers',
            'include' => 'users,users.location',
            'fields' => [
                'customers' => 'name, website',
                'users' => 'first_name, last_name',
                'users.location' => 'street'
            ]
        ];

        $this->query->setQuery($query)->activate();

        $customer = Customer::first();
        $user = $customer->getRelation('users')->first();
        $location = $user->getRelation('location');

        $this->assertEquals(['name','website'], array_keys($customer->attributesToArray()));
        $this->assertEquals(['first_name','last_name'], array_keys($user->attributesToArray()));
        $this->assertEquals(['street'], array_keys($location->attributesToArray()));

        Customer::clearBootedModels();
    }

    /**
     * Test the page based pagination query
     *
     * @return void
     */
    public function testModelPaginationNumberAndSize()
    {
        $query = [
            'resource' => 'customers',
            'include' => 'users',
            'paginate' => [
                'number' => 2,
                'size' => 5
            ]
        ];

        $this->query->setQuery($query)->activate();
        $customers = Customer::all();
        $users = $customers->first()->getRelation('users');
        $pagination = $customers->paginator()->toArray();

        $this->assertInstanceOf(\Luminary\Services\ApiQuery\Pagination\Collection::class, $customers);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $users);
        $this->assertArraySubset(['current_page' => 2, 'per_page' => 5], $pagination);

        Customer::clearBootedModels();
    }

    /**
     * Test a basic filter query
     *
     * @return void
     */
    public function testBasicModelFilter()
    {
        $customer = $this->customers->random();

        $query = [
            'resource' => 'customers',
            'filter' => [
                'id,'.$customer->id,
                'name,'.$customer->name
            ]
        ];

        $expected = [
            'id' => $customer->id,
            'name' => $customer->name,
            'website' => $customer->website,
            'phone' => $customer->phone
        ];

        $this->query->setQuery($query)->activate();

        $results = Customer::all();

        $this->assertEquals(1, $results->count());
        $this->assertEquals($expected, $results->first()->toArray());

        Customer::clearBootedModels();
    }

    /**
     * Test an `AND` and `OR` filter query
     *
     * @return void
     */
    public function testAndOrModelFilters()
    {
        $customers = $this->customers->random(2)->all();
        $customerOne = array_shift($customers);
        $customerTwo = array_pop($customers);

        $query = [
            'resource' => 'customers',
            'filter' => [
                'id,'.$customerOne->id,
                'name,=,'.$customerOne->name,
                'or' => [
                    'id,'.$customerTwo->id,
                    'name,'.$customerTwo->name,
                ]
            ]
        ];

        $expected = [
            [
                'id' => $customerOne->id,
                'name' => $customerOne->name,
                'website' => $customerOne->website,
                'phone' => $customerOne->phone
            ],
            [
                'id' => $customerTwo->id,
                'name' => $customerTwo->name,
                'website' => $customerTwo->website,
                'phone' => $customerTwo->phone
            ]
        ];

        $this->query->setQuery($query)->activate();

        $results = Customer::all();

        $this->assertEquals(2, $results->count());
        $this->assertEquals($expected, $results->toArray());

        Customer::clearBootedModels();
    }

    /**
     * Test a Nested filter query
     *
     * @return void
     */
    public function testNestedModelFilters()
    {
        $customers = $this->customers->random(2)->all();
        $customerOne = array_shift($customers);
        $customerTwo = array_pop($customers);

        $query = [
            'resource' => 'customers',
            'filter' => [
                'nested' => [
                    'id,'.$customerOne->id,
                    'name,=,'.$customerOne->name,
                    'or' => [
                        'id,'.$customerTwo->id,
                        'name,'.$customerTwo->name,
                    ]
                ]
            ]
        ];

        $expected = [
            [
                'id' => $customerOne->id,
                'name' => $customerOne->name,
                'website' => $customerOne->website,
                'phone' => $customerOne->phone
            ],
            [
                'id' => $customerTwo->id,
                'name' => $customerTwo->name,
                'website' => $customerTwo->website,
                'phone' => $customerTwo->phone
            ]
        ];

        $this->query->setQuery($query)->activate();

        $results = Customer::all();

        $this->assertEquals(2, $results->count());
        $this->assertEquals($expected, $results->toArray());

        Customer::clearBootedModels();
    }

    /**
     * Test an advanced nested filter query
     *
     * @return void
     */
    public function testAdvancedNestedModelFilters()
    {
        $customers = $this->customers->random(2)->all();
        $customerOne = array_shift($customers);
        $customerTwo = array_pop($customers);
        $location = $customerOne->users->first()->location;

        $query = [
            'resource' => 'customers',
            'include' => 'users,users.location',
            'filter' => [
                'nested' => [
                    'id,'.$customerOne->id,
                    'name,=,'.$customerOne->name,
                    'or' => [
                        'id,'.$customerTwo->id,
                        'name,'.$customerTwo->name,
                    ]
                ],
                'users.location' => [
                    'nested' => [
                        'id,'.$location->id
                    ]
                ]
            ]
        ];

        $expected = [
            [
                'id' => $customerOne->id,
                'name' => $customerOne->name,
                'website' => $customerOne->website,
                'phone' => $customerOne->phone
            ],
            [
                'id' => $customerTwo->id,
                'name' => $customerTwo->name,
                'website' => $customerTwo->website,
                'phone' => $customerTwo->phone
            ]
        ];

        $this->query->setQuery($query)->activate();

        $results = Customer::all();

        $this->assertEquals(2, $results->count());

        $results->each(
            function($customer, $i) use($expected, $location){
                $array = array_except($customer->toArray(), ['users']);
                $this->assertEquals($expected[$i], $array);
                $customer->users->each(
                    function($user) use($location) {
                        $l = $user->location;

                        if(! is_null($l)) {
                            $this->assertEquals($location->id, $l->id);
                        }
                    }
                );
            }
        );

        Customer::clearBootedModels();
    }

    /**
     * Test simple sorting
     *
     * @return void
     */
    public function testSortingScope()
    {
        $query = [
            'resource' => 'customers',
            'include' => 'users,users.location',
            'sort' => [
                '-name',
                'users.first_name',
                'users.location.name'
            ]
        ];

        $this->query->setQuery($query)->activate();
        $results = Customer::limit(10)->get();
        $users = $results->first()->getRelation('users');
        $location = $users->first()->getRelation('location');

        // The query should sort the customer results by name DESC
        $this->assertEquals(
            $results->pluck('name')->shuffle()->sort()->values()->reverse()->values()->all(),
            $results->pluck('name')->all()
        );

        // The query should sort the a customer user results by first_name ASC
        $this->assertEquals(
            $users->pluck('first_name')->shuffle()->sort()->values()->all(),
            $users->pluck('first_name')->all()
        );

        // Lets make sure that we still get a location
        // even though there is a sorting query w/a hasOne
        $this->assertNotEmpty($location);

        Customer::clearBootedModels();
    }

    /**
     * Seed the Test Database
     * 
     * @return void
     */
    protected function seed()
    {
        $users = collect();
        $locations = factory(Location::class, 5)->create();
        $customers = factory(Customer::class, 20)
            ->create()
            ->each(function($customer) use($users) {
                $users->push($customer->users()->save(factory(User::class)->make()));
                $users->push($customer->users()->save(factory(User::class)->make()));
                $users->push($customer->users()->save(factory(User::class)->make()));
            });

        $users->each(function($user) use($locations) {
            $user->location()->associate($locations->random())->save();
        });

        $this->customers = $customers;
        $this->locations = collect($locations);
        $this->users = $users;
    }
}