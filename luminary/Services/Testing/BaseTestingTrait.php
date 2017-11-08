<?php

namespace Luminary\Services\Testing;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Luminary\Services\ApiQuery\Query;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\Interest;
use Luminary\Services\Testing\Models\Location;
use Luminary\Services\Testing\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait BaseTestingTrait
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
     * The interest collection
     *
     * @return \Illuminate\Support\Collection
     */
    protected $interests;

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
     * Return the default request headers
     *
     * @return array
     */
    protected function headers()
    {
        return [
            'CONTENT_TYPE' => 'application/vnd.api+json'
        ];
    }

    /**
     * Transform headers array to array of $_SERVER vars with HTTP_* format.
     *
     * @param  array  $headers
     * @return array
     */
    protected function transformHeadersToServerVars(array $headers)
    {
        $headers = collect($this->headers())->merge($headers)->all();
        return parent::transformHeadersToServerVars($headers);
    }

    /**
     * Setup the routes for running middleware tests
     *
     * @return void
     */
    protected function setUpRoutes()
    {
        $app = app();
        $router = $app->router;

        $router->get('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $router->post('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $router->put('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $router->patch('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $router->delete('api-query-middleware', function () {
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
     * @param int $customerCount
     * @param int $userCount
     * @param int $locationCount
     */
    protected function seed(int $customerCount = 20, int $userCount = 3, $locationCount = 5)
    {
        $this->createRolesAndPermissions();

        $users = collect();
        $locations = factory(Location::class, $locationCount)->create();
        $interests = factory(Interest::class, 10)->create();
        $customers = factory(Customer::class, $customerCount)
            ->create()
            ->each(function (Customer $customer) use ($users, $userCount, $locations, $interests) {
                $tenant_id = $customer->tenant_id;
                for ($i=0; $i < $userCount; $i++) {
                    $user = factory(User::class)->make();
                    $user->tenant_id = $tenant_id;
                    $user = $customer->users()->save($user);
                    $customer->interests()->sync($interests->pluck('id')->random(5));
                    $users->push($user);
                }
                $locations = $locations->where('tenant_id', $tenant_id);
                $customer->location()->associate($locations->random())->save();
            });

        $users->each(function (User $user) use ($locations) {
            $locations = $locations->where('tenant_id', $user->tenant_id);
            $user->location()->associate($locations->random())->save();
        });

        $this->customers = $customers;
        $this->locations = collect($locations);
        $this->interests = $interests;
        $this->users = $users;
    }

    /**
     * Create roles and permissions for testing
     *
     * @return void
     */
    protected function createRolesAndPermissions()
    {
        $roles = ['user', 'admin'];
        $entities = ['customers', 'locations', 'interests', 'users'];
        $permissions = ['view', 'create', 'update', 'delete'];
        $rolePermissions = [
            'user' =>  [
                'locations.view', 'interests.view'
            ],
            'admin' => [
                'customers.view', 'customers.create', 'customers.update', 'customers.delete',
                'locations.view', 'locations.create', 'locations.update', 'locations.delete',
                'interests.view', 'interests.create', 'interests.update', 'interests.delete',
                'users.view', 'users.create', 'users.update', 'users.delete',
            ]
        ];

        // Create Roles
        $roles = collect($roles)->flip()->transform(function ($value, $role) use ($permissions) {
            return Role::create(['name' => $role]);
        });

        // Create permissions
        collect($entities)->flip()->transform(function ($value, $entity) use ($permissions) {
            return collect($permissions)->each(function ($permission) use ($entity) {
                Permission::create(['name' => $entity . '.' . $permission ]);
            });
        });

        // Attache Roles and Permissions
        $roles->each(function ($role) use ($rolePermissions) {
            $role->givePermissionTo($rolePermissions[$role->name]);
        });
    }
}
