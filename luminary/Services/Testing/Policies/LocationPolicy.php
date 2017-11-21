<?php

namespace Luminary\Services\Testing\Policies;

use Luminary\Services\Testing\Models\Location as Model;

class LocationPolicy
{
    /**
     * Can the user view
     *
     * @param $user
     * @return bool
     */
    public function view($user)
    {
        return $user->can($this->type() . '.view', Model::class);
    }

    /**
     * Can the user create
     *
     * @param $user
     * @return bool
     */
    public function create($user)
    {
        return $user->can($this->type() . '.create', Model::class);
    }

    /**
     * Can the user update
     *
     * @param $user
     * @return bool
     */
    public function update($user)
    {
        return $user->can($this->type() . '.update', Model::class);
    }

    /**
     * Can the user delete
     *
     * @param $user
     * @return bool
     */
    public function delete($user)
    {
        return $user->can($this->type() . '.delete', Model::class);
    }

    /**
     * Get the model type
     *
     * @return string
     */
    private function type()
    {
        return (new Model)->getType();
    }
}
