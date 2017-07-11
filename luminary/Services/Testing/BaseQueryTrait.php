<?php

namespace Luminary\Services\Testing;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Luminary\Services\ApiQuery\Query;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\Location;
use Luminary\Services\Testing\Models\User;

trait BaseQueryTrait
{
    use DatabaseMigrations;

    /**
     * The fully generated url
     *
     * @var string
     */
    protected $url;

    /**
     * The query instance
     *
     * @var \Luminary\Services\ApiQuery\Query
     */
    protected $query;

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
     * Get the query instance as an array
     *
     * @return array
     */
    protected function getQueryArray()
    {
        return $this->query->toArray();
    }

    /**
     * Setup the routes for running middleware tests
     *
     * @return void
     */
    protected function setUpRoutes()
    {
        $app = app();

        $app->get('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $app->post('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $app->put('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $app->patch('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $app->delete('api-query-middleware', function () {
            return response('api query middleware', 200);
        });
    }

    /**
     * Create the HTTP url string w/parameters for testing
     *
     * @return void
     */
    protected function setUpUrl()
    {
        $this->url = '/api-query-middleware?' . http_build_query($this->queryString);
    }

    /**
     * Setup the query instance for testing
     *
     * @return void
     */
    protected function setUpQuery()
    {
        $this->query = app(Query::class)->activate();
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
            ->each(function ($customer) use ($users) {
                $users->push($customer->users()->save(factory(User::class)->make()));
                $users->push($customer->users()->save(factory(User::class)->make()));
                $users->push($customer->users()->save(factory(User::class)->make()));
            });

        $users->each(function ($user) use ($locations) {
            $user->location()->associate($locations->random())->save();
        });

        $this->customers = $customers;
        $this->locations = collect($locations);
        $this->users = $users;
    }
}
