<?php

namespace Luminary\Services\ApiRequest\Traits;

use Luminary\Services\ApiRequest\Exceptions\ForbiddenAttribute;

trait HasForbiddenAttribute
{
    /**
     * Check for a request containing a forbidden attribute
     *
     * @param $attribute
     * @param $data
     */
    public function hasForbiddenAttribute($attribute, $data) :void
    {
        $attributes = array_get($data, 'attributes', []);

        if (array_key_exists($attribute, $attributes)) {
            $this->throwForbiddenAttributeException($attribute);
        }
    }

    /**
     * Throw a new missing data attribute exception
     *
     * @param string $attribute
     * @throws \Luminary\Services\ApiRequest\Exceptions\ForbiddenAttribute
     * @return void
     */
    public function throwForbiddenAttributeException(string $attribute) :void
    {
        $message = 'The attribute supplied is forbidden for this request';
        throw new ForbiddenAttribute($attribute, $message);
    }
}
