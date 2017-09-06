<?php

namespace Luminary\Services\Auth\Contracts;

interface AuthorizesWhenResolved
{
    /**
     * Authorize the class instance.
     *
     * @return void
     */
    public function authorizeInstance();
}
