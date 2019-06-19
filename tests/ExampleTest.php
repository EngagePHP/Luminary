<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Luminary\Services\Auth\Repositories\AuthRepository;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\User;

class ExampleTest extends TestCase
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
        TenantModelScope::setOverride();
        $this->setConfig();
        $this->seed(2, 2, 2);
    }

    /**
     * Set the User model provider for testing
     *
     * @return void
     */
    public function setConfig()
    {
        $config = config('auth');
        array_set($config, 'providers.users.model', User::class);

        config(['auth' => $config]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = $this->users->first();
        $login = AuthRepository::login(['email' => $user->email, 'password' => 'secret']);
        $token = array_get($login, 'access_token');
        $headers = [
            'content-type' => 'application/vnd.api+json',
            'Authorization' => 'bearer '. $token
        ];

        $this->get('/', $headers);
        $content = json_decode($this->response->getContent(), true);
        $data = array_get($content, 'data');

        $this->assertEquals(
            [$this->app->version()], $data
        );
    }
}
