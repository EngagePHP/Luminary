<?php

namespace Luminary\Services\Auth\Contracts;

interface Authorize
{
    /**
     * Can created a record
     *
     * @return bool
     */
    public function create() :bool;

    /**
     * Can delete a record
     *
     * @return bool
     */
    public function delete() :bool;

    /**
     * Can retrieve a list of records
     *
     * @return bool
     */
    public function list() :bool;

    /**
     * Can read a record
     *
     * @return bool
     */
    public function read() :bool;

    /**
     * Can update a record
     *
     * @return bool
     */
    public function update() :bool;
}
