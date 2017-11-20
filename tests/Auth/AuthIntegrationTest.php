<?php

use Luminary\Services\Auth\Repositories\AuthRepository;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\User;

class AuthIntegrationTest extends TestCase
{
    use Luminary\Services\Testing\BaseTestingTrait;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
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
     * Return an array of authorization headers
     *
     * @return array
     */
    public function headers()
    {
        return ['CONTENT_TYPE' => 'application/json'];
    }

    /**
     * Test the successful login
     * from the repository method
     *
     * @return void
     */
    public function testAuthLoginResponse()
    {
        $user = $this->users->first();
        $data = [
            'email' => $user->email,
            'password' => 'secret'
        ];

        $this->json('POST', 'auth/login', $data, $this->headers());

        $this->seeJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    /**
     * Test that exception is thrown with
     * incorrect credentials
     *
     * @return void
     */
//    public function testAuthLoginFailure()
//    {
//        $user = $this->users->first();
//        $data = [
//            'email' => $user->email,
//            'password' => 'secret'
//        ];
//
//        $this->json('POST', 'auth/login', $data, $this->headers());
//
//        should return unauthorized error
//    }

    /**
     * Test Authentication token refresh
     *
     * @return void
     */
    public function testAuthTokenRefresh()
    {
        $user = $this->users->first();
        $login = AuthRepository::login(['email' => $user->email, 'password' => 'secret']);
        $token = array_get($login, 'access_token');
        $headers = array_merge(
            $this->headers(),
            ['Authorization' => 'bearer '. $token]
        );

        $this->json('POST', 'auth/refresh', [], $headers);
        $refresh = array_get($this->response->getOriginalContent(), 'access_token');

        $this->assertNotEquals($token, $refresh);
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
        $token = array_get($login, 'access_token');
        $headers = array_merge(
            $this->headers(),
            ['Authorization' => 'bearer '. $token]
        );

        $this->assertNotEmpty(AuthRepository::user());

        $this->json('POST', 'auth/logout', [], $headers);

        $this->assertEmpty(AuthRepository::user());
    }

    /**
     * Test the returned object for the
     * authenticated user
     *
     * @return void
     */
    public function testAuthUser()
    {
        $user = $this->users->first();
        $login = AuthRepository::login(['email' => $user->email, 'password' => 'secret']);
        $token = array_get($login, 'access_token');
        $headers = array_merge(
            $this->headers(),
            ['Authorization' => 'bearer '. $token]
        );

        $user = AuthRepository::user();

        $this->json('GET', 'auth/user', [], $headers);

        $responseUser = $this->response->getOriginalContent();
        $responseUserId = array_get($responseUser, 'data.id');

        $this->assertEquals($user->id, $responseUserId);
    }

    /**
     * Test incorrect token fails
     *
     * @return void
     */
    public function testAuthUserFails()
    {
        $headers = array_merge(
            $this->headers(),
            ['Authorization' => 'bearer 1234']
        );

        $this->json('GET', 'auth/user', [], $headers);

        $responseUser = $this->response->getOriginalContent();
        $response = array_get($responseUser, 'errors.0.detail');

        $this->assertEquals('Wrong number of segments', $response);
    }
}
