<?php

use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\BaseTestingTrait;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\Location;
use Luminary\Services\Testing\Models\User;

class GeneratedRelationshipControllersTest extends TestCase
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

        $this->json('GET', 'customers/' . $customer . '/relationships/users', [], $this->headers());
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
        $customer = $this->customers->first();
        $originalUsers = $customer->users()->get(['id'])->pluck('id');
        $ids = $originalUsers->random(3)->sort()->values()->all();

        $data = ['data' => array_map(
            function($id) {
               return [
                   'type' => 'users',
                   'id' => $id
               ];
            }
        ,  $ids)];

        $this->json('POST', 'customers/' . $customer->id . '/relationships/users', $data , $this->headers());

        $response = json_decode($this->response->getContent(), true);

        $this->assertResponseStatus(204);
        $this->assertNull($response);
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
        $originalUsers = $customer->users()->get(['id'])->pluck('id');
        $ids = $originalUsers->random(3)->sort()->values();

        $data = [
            'data' => $ids->map(
                function($id) {
                    return [
                        'type' => 'users',
                        'id' => $id
                    ];
                }
            )->all()
        ];

        $this->json('PATCH', 'customers/' . $customer->id . '/relationships/users/', $data, $this->headers());

        $response = json_decode($this->response->getContent(), true);

        $this->assertResponseStatus(204);
        $this->assertNull($response);
    }

    /**
     * Test that the Delete method returns the correct response
     *
     * @return void
     */
    public function testDeleteMethod() :void
    {
        $customer = $this->customers->first();
        $user = $customer->users()->first();

        $data = [
            'data' => [[
                'type' => 'users',
                'id' => $user->id
            ]]
        ];

        $this->json('DELETE', 'customers/' . $customer->id . '/relationships/users', $data, $this->headers());

        $this->assertEmpty($this->response->getContent());
        $this->assertResponseStatus(204);
    }
}
