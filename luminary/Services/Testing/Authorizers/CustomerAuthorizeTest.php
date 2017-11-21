<?php

namespace Luminary\Services\Testing\Authorizers;

use Luminary\Services\Auth\Authorize;
use Illuminate\Support\Facades\Gate;
use Luminary\Services\Testing\Models\Customer as Model;

class CustomerAuthorizeTest extends Authorize
{
    /**
     * Can created a record
     *
     * @return bool
     */
    public function create() :bool
    {
        return Gate::allows('create', Model::class);
    }

    /**
     * Can delete a record
     *
     * @return bool
     */
    public function delete() :bool
    {
        return Gate::allows('delete', Model::class);
    }

    /**
     * Can retrieve a list of records
     *
     * @return bool
     */
    public function list() :bool
    {
        return Gate::allows('view', Model::class);
    }

    /**
     * Can read a record
     *
     * @return bool
     */
    public function read() :bool
    {
        return Gate::allows('view', Model::class);
    }

    /**
     * Can update a record
     *
     * @return bool
     */
    public function update() :bool
    {
        return Gate::allows('update', Model::class);
    }
}
