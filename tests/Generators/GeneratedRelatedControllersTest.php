<?php

use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\BaseTestingTrait;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\Location;
use Luminary\Services\Testing\Models\User;

class GeneratedRelatedControllersTest extends TestCase
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
        $customer = $this->customers->random()->first()->id;
        $this->json('GET', 'customers/' . $customer . '/users', [], $this->headers());
        $response = json_decode($this->response->getContent(), true);
        $data = array_get($response, 'data');

        $this->assertEquals($this->customers->count(), count($data));
        $this->assertResponseOk();
    }

    /**
     * Test the retrival of an individual
     * relationship
     *
     * @return void
     */
    public function testShowMethod() :void
    {
        $customer = $this->customers->random()->first();
        $user = $customer->users()->first();

        $this->json('GET', 'customers/' . $customer->id . '/users/' . $user->id, [], $this->headers());
        $response = json_decode($this->response->getContent(), true);
        $data = array_get($response, 'data');

        $this->assertResponseOk();
        $this->assertEquals(array_except($user->toArray(), ['id']), array_get($data, 'attributes'));
        return;
    }

    /**
     * Test that the store method returns the correct response
     * and format
     *
     * @return void
     */
    public function testStoreMethod() :void
    {
        $customer = factory(Customer::class)->create();

        $user = factory(User::class)->make()->toArray();

        $data = ['data' => [
            'type' => 'users',
            'attributes' => $user
        ]];

        $this->json('POST', 'customers/' . $customer->id . '/users', $data , $this->headers());

        $response = json_decode($this->response->getContent(), true);
        $results = array_get($response, 'data');
        $attributes = array_get($results, 'attributes');

        $this->assertArrayHasKey('id', $results);
        $this->assertEquals(array_except($user, ['password']), array_except($attributes, ['customer_id', 'password']));
        $this->assertEquals($customer->id, array_get($attributes, 'customer_id'));
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
        $customer = $this->customers->first();
        $customer->setRelations([]);
        $location = $customer->location()->first();
        $attributes = factory(Location::class)->make()->toArray();

        $data = [
            'data' => [
                'type' => 'location',
                'id' => $location->id,
                'attributes' => $attributes
            ]
        ];

        $this->json('PATCH', 'customers/' . $customer->id . '/location/' . $location->id, $data, $this->headers());

        $response = json_decode($this->response->getContent(), true);
        $results = array_get($response, 'data');
        $expected = array_merge(array_except($location->toArray(), ['id']), $attributes);

        $this->assertResponseStatus(200);
        $this->assertEquals((string) $location->id, array_get($results, 'id'));
        $this->assertEquals($expected, array_get($results, 'attributes'));
        $this->assertEmpty(array_get($results, 'relationships'));
    }

    /**
     * Test that the Delete method returns the correct response
     *
     * @return void
     */
    public function testDeleteMethod() :void
    {
        $customer = $this->customers->first();
        $user = $customer->users()->get()->first();

        $this->json('DELETE', 'customers/' . $customer->id . '/users/' . $user->id , [], $this->headers());

        $this->assertEmpty($this->response->getContent());
        $this->assertResponseStatus(204);
    }
}
