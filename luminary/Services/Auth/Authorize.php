<?php

namespace Luminary\Services\Auth;

class Authorize implements Contracts\Authorize
{
    /**
     * Can created a record
     *
     * @return bool
     */
    public function create() :bool
    {
        return true;
    }

    /**
     * Can delete a record
     *
     * @return bool
     */
    public function delete() :bool
    {
        return true;
    }

    /**
     * Can retrieve a list of records
     *
     * @return bool
     */
    public function list() :bool
    {
        return true;
    }

    /**
     * Can read a record
     *
     * @return bool
     */
    public function read() :bool
    {
        return true;
    }

    /**
     * Can update a record
     *
     * @return bool
     */
    public function update() :bool
    {
        return true;
    }
}
