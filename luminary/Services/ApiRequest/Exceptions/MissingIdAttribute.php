<?php

namespace Luminary\Services\ApiRequest\Exceptions;

class MissingIdAttribute extends MissingDataAttribute
{
    /**
     * Set the response pointer source
     *
     * @return array
     */
    public function getSource()
    {
        return [
            'pointer' => '/data/id'
        ];
    }
}
