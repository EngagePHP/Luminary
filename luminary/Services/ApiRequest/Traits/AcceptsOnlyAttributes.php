<?php

namespace Luminary\Services\ApiRequest\Traits;

use Luminary\Exceptions\MultiException;
use Luminary\Services\ApiRequest\Exceptions\UnacceptedAttribute;

trait AcceptsOnlyAttributes
{
    /**
     * Check that the request has only accepted attributes
     *
     * @param $data
     * @param array $accepted
     * @return void
     */
    public function hasAcceptedAttributes($data, array $accepted) :void
    {
        $inputs = array_except($data, $accepted);

        if (count($inputs)) {
            $this->throwUnacceptedAttributeException(array_keys($inputs), $accepted);
        }
    }

    /**
     * Throw a new missing data attribute exception
     *
     * @param array $attributes
     * @param array $accepted
     * @return void
     */
    public function throwUnacceptedAttributeException(array $attributes, array $accepted) :void
    {
        $pop = count($attributes) > 1 ? ', and ' . array_pop($accepted) : '';
        $message = 'Only '. implode(', ', $accepted) . $pop .' are accepted in the data object.';

        $exceptions = array_map(
            function ($attribute) use ($message) {
                return new UnacceptedAttribute($attribute, $message);
            },
            $attributes
        );

        throw new MultiException($exceptions);
    }
}
