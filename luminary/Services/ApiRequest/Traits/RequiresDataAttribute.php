<?php

namespace Luminary\Services\ApiRequest\Traits;

use Luminary\Services\ApiRequest\Exceptions\MissingDataAttribute;

trait RequiresDataAttribute
{
    /**
     * Check that the data attribute exists
     *
     * @param array $input
     * @return void
     */
    public function dataAttributeExists(array $input) :void
    {
        if (! array_key_exists('data', $input)) {
            $this->throwMissingDataAttributeException();
        }
    }

    /**
     * Throw a new missing data attribute exception
     *
     * @throws \Luminary\Services\ApiRequest\Exceptions\MissingDataAttribute
     * @return void
     */
    public function throwMissingDataAttributeException() :void
    {
        $message = 'Missing data attribute for create request';
        throw new MissingDataAttribute($message);
    }
}
