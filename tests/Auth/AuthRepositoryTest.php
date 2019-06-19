<?php

use Illuminate\Validation\UnauthorizedException;
use Luminary\Services\Auth\Repositories\AuthRepository;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\User;

class AuthRepositoryTest extends TestCase
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
     * Test the successful login
     * from the repository method
     *
     * @return void
     */
    public function testAuthLoginSuccess()
    {
        $user = $this->users->first();
        $login = AuthRepository::login(['email' => $user->email, 'password' => 'secret']);

        $this->assertEquals(['access_token', 'token_type', 'expires_in'], array_keys($login));
    }

    /**
     * Test that exception is thrown with
     * incorrect credentials
     *
     * @return void
     */
    public function testAuthLoginFailure()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('The username/email or password are incorrect');

        $user = $this->users->first();
        AuthRepository::login(['email' => $user->email, 'password' => 'bar']);
    }

    /**
     * Test Authentication token refresh
     *
     * @return void
     */
    public function testAuthTokenRefresh()
    {
        $user = $this->users->first();
        $login = AuthRepository::login(['email' => $user->email, 'password' => 'secret']);

        // Create the request
        $request = app('request')->create('http://example.com/api', 'GET');
        $headerValue = array_get($login, 'token_type') . ' ' . array_get($login, 'access_token');
        $request->headers->set('Authorization', $headerValue);

        // Set the request
        AuthRepository::guard()->setRequest($request);

        // Create the refresh token
        $refresh = AuthRepository::refresh();

        $this->assertNotEquals($login['access_token'], $refresh['access_token']);
    }

    /**
     * Test Authentication logout
     *
     * @return void
     */
    public function testAuthLogout()
    {
        $user = $this->users->first();
        $login = AuthRepository::login(['email' => $user->email, 'password' => 'secret']);

        // Create the request
        $request = app('request')->create('http://example.com/api', 'GET');
        $headerValue = array_get($login, 'token_type') . ' ' . array_get($login, 'access_token');
        $request->headers->set('Authorization', $headerValue);

        // Set the request
        AuthRepository::guard()->setRequest($request);

        $this->assertNotEmpty(AuthRepository::user());

        AuthRepository::logout();

        $this->assertEmpty(AuthRepository::user());
    }
}
