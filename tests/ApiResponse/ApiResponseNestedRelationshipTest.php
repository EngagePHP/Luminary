<?php

use Luminary\Services\Tenants\TenantModelScope;

class ApiResponseNestedRelationshipTest extends TestCase
{
    use Luminary\Services\Testing\BaseTestingTrait;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(10, 2, 2);
        $this->app->middleware(\Luminary\Services\ApiQuery\QueryMiddleware::class);
        $this->setUpQuery();
    }

    /**
     * Test a collection of models and their includes
     *
     * @return void
     */
    public function testNestedCollectionSerializerResponse()
    {
        TenantModelScope::setOverride();
        $this->get('customers?include=location,location.users');
        TenantModelScope::setOverride(false);


        $response = $this->response->getOriginalContent();

        $included = collect(array_get($response, 'included'))->groupBy('type');
        $locations = collect($included->get('locations'));
        $users = collect($included->get('users'));

        // Should return 2 locations
        $this->assertEquals(2, $locations->count());

        // For each location assure that we have the correct amount
        // of includes
        $locations->each(function($location) use($users) {
            $locationUsers = array_get($location, 'relationships.users.data');
            $ids = collect($locationUsers)->pluck('id')->all();
            $collection = $users->filter(function($user) use($ids) {
                return in_array($user['id'], $ids);
            });

            $this->assertEquals(count($ids), $collection->count());
        });
    }

    /**
     * Test a single model response with nested includes
     *
     * @return void
     */
    public function testNestedModelSerializerResponse()
    {
        TenantModelScope::setOverride();
        $this->get('customers/1?include=location,location.users');
        TenantModelScope::setOverride(false);

        $response = $this->response->getOriginalContent();

        $included = collect(array_get($response, 'included'))->groupBy('type');
        $locations = collect($included->get('locations'));
        $users = collect($included->get('users'));

        // Should return 1 location
        $this->assertEquals(1, $locations->count());

        // For the location assure that we have the correct amount
        // of includes
        $locations->each(function($location) use($users) {
            $locationUsers = array_get($location, 'relationships.users.data');
            $ids = collect($locationUsers)->pluck('id')->all();
            $collection = $users->filter(function($user) use($ids) {
                return in_array($user['id'], $ids);
            });

            $this->assertEquals(count($ids), $collection->count());
        });
    }
}