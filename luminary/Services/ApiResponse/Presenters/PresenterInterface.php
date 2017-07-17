<?php

namespace Luminary\Services\ApiResponse\Presenters;

use Illuminate\Http\Request;
use Luminary\Services\ApiQuery\Query;
use Luminary\Services\ApiResponse\Serializers\SerializerInterface;

interface PresenterInterface
{
    /**
     * Return the error response array
     *
     * @return array
     */
    public function format() :array;
}
