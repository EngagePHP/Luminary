<?php

class UserObserverTraitTest extends TestCase
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

        $this->seed(2, 2, 2);
    }

    /**
     * Test the hashing of password
     * on user create
     *
     * @return void
     */
    public function testUserPasswordShouldBeHashed()
    {
        $user = $this->users->first();
        $check = app('hash')->check('secret', $user->password);

        $this->assertTrue($check);
    }

    /**
     * Test that the user password
     * is hashed when the password
     * property is updated
     *
     * @return void
     */
    public function testUserPasswordShouldBeHashedOnUpdate()
    {
        $user = $this->users->first();
        $password = 'changed';

        $user->password = $password;
        $user->save();

        $check = app('hash')->check('changed', $user->password);

        $this->assertTrue($check);
    }

    /**
     * Test that the user password
     * password is not rehashed if
     * it has not been updated
     *
     * @return void
     */
    public function testUserPasswordNotRehashedOnUpdate()
    {
        $user = $this->users->first();

        $user->first_name = 'ted';
        $user->save();

        $check = app('hash')->check('secret', $user->password);

        $this->assertTrue($check);
    }
}
