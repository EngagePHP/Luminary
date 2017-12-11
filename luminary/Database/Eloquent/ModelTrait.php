<?php

namespace Luminary\Database\Eloquent;

use Luminary\Services\ApiQuery\QueryTrait;
use Luminary\Services\ApiResponse\ResponseModelTrait;
use Luminary\Services\Timezone\TimezoneModelTrait;

trait ModelTrait
{
    use QueryTrait;
    use ResponseModelTrait;
    use TimezoneModelTrait;

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        // @todo: find out the implementation for laravel 5.5
    }

    /**
     * Get the connection of the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection()
    {
        // @todo: find out the implementation for laravel 5.5
    }
}
