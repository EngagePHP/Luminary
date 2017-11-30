<?php

namespace Luminary\Services\Users;

class UserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  \Luminary\Services\Users\User $user
     * @return void
     */
    public function creating(User $user)
    {
        // Hash the user password
        $user->password = $this->hash($user->password);
    }

    /**
     * Listen to the User created event.
     *
     * @param  \Luminary\Services\Users\User $user
     * @return void
     */
    public function updating(User $user)
    {
        // Hash a new user password when updating
        if ($this->shouldUpdatePassword($user)) {
            $user->password = $this->hash($user->password);
        }
    }

    /**
     * Create a hashed string
     *
     * @param string $password
     * @return string
     */
    protected function hash(string $password)
    {
        return app('hash')->make($password);
    }

    /**
     * Is there a new user password that
     * should be hashed?
     *
     * @param User $user
     * @return bool
     */
    protected function shouldUpdatePassword(User $user)
    {
        $dirty = $user->isDirty('password');
        $original = $user->getOriginal('password');
        $new = $user->password;

        return $dirty && ($new !== $original);
    }
}
