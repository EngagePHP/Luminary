<?php

use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\BaseTestingTrait;
use Luminary\Services\Testing\Models\Customer;

class GeneratedControllersTest extends TestCase
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
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(5,5,5);

        TenantModelScope::setOverride();
    }

    /**
     * Test the index method returns
     * all of the customers
     *
     * @return void
     */
    public function testIndexMethod() :void
    {
        $this->json('GET', 'customers', [], $this->headers());
        $response = json_decode($this->response->getContent(), true);
        $data = array_get($response, 'data');

        $this->assertEquals($this->customers->count(), count($data));
        $this->assertResponseOk();
    }

    /**
     * Test that the store method returns the correct response
     * and format
     *
     * @return void
     */
    public function testStoreMethod() :void
    {
        $attributes = factory(Customer::class, 1)->make()->first()->toArray();

        $data = [
            'data' => [
                'type' => 'customers',
                'attributes' => $attributes
            ]
        ];

        $this->json('POST', 'customers', $data , $this->headers());

        $response = json_decode($this->response->getContent(), true);
        $results = array_get($response, 'data');
        $this->assertArrayHasKey('id', $results);
        $this->assertEquals($attributes, array_get($results, 'attributes'));
        $this->assertResponseStatus(201);
    }

    /**
     * Test that the store method returns the correct response
     * and format with relationships
     *
     * @return void
     */
    public function testStoreMethodWithRelationships() :void
    {
        $attributes = factory(Customer::class, 1)->make()->first()->toArray();
        $location = $this->locations->first()->id;
        $users = $this->users->take(3)->pluck('id')->all();
        $interests = $this->interests->take(3)->pluck('id')->all();

        $data = [
            'data' => [
                'type' => 'customers',
                'attributes' => $attributes,
                'relationships' => [
                    'location' => [
                        'data' => [
                            'type' => 'location',
                            'id' => $location
                        ]
                    ],
                    'users' => [
                        'data' => array_map(function($id) { return ['type' => 'users', 'id' => $id ];}, $users)
                    ],
                    'interests' => [
                        'data' => array_map(function($id) { return ['type' => 'interests', 'id' => $id ];}, $interests)
                    ]
                ]
            ]
        ];

        $this->json('POST', 'customers', $data , $this->headers());

        $attributes = array_add($attributes, 'location_id', $location);
        $response = json_decode($this->response->getContent(), true);
        $results = array_get($response, 'data');
        $relationships = collect(array_get($results,'relationships'))->map(function($r) {
            return array_only($r, 'data');
        })->all();

        $relationshipsExpected = array_get($data, 'data.relationships');
        array_set($relationshipsExpected, 'location.data.type', 'locations');

        $this->assertArrayHasKey('id', $results);
        $this->assertEquals($attributes, array_get($results, 'attributes'));
        $this->assertEquals($relationshipsExpected, $relationships);
        $this->assertResponseStatus(201);
    }

    /**
     * Test that the update method returns the correct response
     * and format
     *
     * @return void
     */
    public function testUpdateMethod() :void
    {
        $attributes = factory(Customer::class, 1)->make()->first()->toArray();
        $customer = $this->customers->first();
        $customer->setRelations([]);
        $id = $customer->id;

        $data = [
            'data' => [
                'type' => 'customers',
                'id' => $id,
                'attributes' => $attributes
            ]
        ];



        $this->json('PATCH', 'customers/' . $id, $data, $this->headers());

        $response = json_decode($this->response->getContent(), true);
        $results = array_get($response, 'data');
        $expected = array_merge(array_except($customer->toArray(), ['id']), $attributes);

        $this->assertResponseStatus(200);
        $this->assertEquals((string) $id, array_get($results, 'id'));
        $this->assertEquals($expected, array_get($results, 'attributes'));
        $this->assertEmpty(array_get($results, 'relationships'));
    }

    /**
     * Test that the update method returns the correct response
     * and format with relationships
     *
     * @return void
     */
    public function testUpdateMethodWithRelationships() :void
    {
        $attributes = factory(Customer::class, 1)->make()->first()->toArray();
        $customer = $this->customers->first();
        $customerRelations = $customer->load('location', 'users')->getRelations();
        $customer->setRelations([]);
        $id = $customer->id;
        $location = $this->locations->first()->id;
        $users = $this->users->take(3)->pluck('id');

        $data = [
            'data' => [
                'type' => 'customers',
                'id' => $id,
                'attributes' => $attributes,
                'relationships' => [
                    'location' => [
                        'data' => [
                            'type' => 'location',
                            'id' => $location
                        ]
                    ],
                    'users' => [
                        'data' => array_map(function($id) { return ['type' => 'users', 'id' => $id ];}, $users->all())
                    ]
                ]
            ]
        ];

        $this->json('PATCH', 'customers/'.$id, $data , $this->headers());

        $response = json_decode($this->response->getContent(), true);
        $results = array_get($response, 'data');
        $customerExpected = array_merge(array_except($customer->toArray(), ['id']), $attributes, ['location_id' => $location]);

        $this->assertResponseStatus(200);
        $this->assertEquals((string) $id, array_get($results, 'id'));

        // Assert Resource
        $this->assertEquals($customerExpected, array_get($results, 'attributes'));

        //Assert Location Relationship
        $this->assertEquals($customerRelations['location']->id, $customer->location_id);
        $this->assertEquals(array_get($results, 'attributes.location_id'), $location);

        // Assert Users Relationship
        $usersExpected = $users->sort()->values();
        $usersResults = collect(array_get($results, 'relationships.users.data'))->pluck('id')->sort()->values();

        $this->assertEquals($usersExpected, $usersResults);
    }

    /**
     * Test that the Delete method returns the correct response
     *
     * @return void
     */
    public function testDeleteMethod() :void
    {
        $id = $this->customers->first()->id;

        $this->json('DELETE', 'customers/'.$id, [], $this->headers());

        $this->assertEmpty($this->response->getContent());
        $this->assertResponseStatus(204);
    }
}
