<?php

namespace Luminary\Services\Sanitation\Contracts;

interface SanitizerArguments
{
    /**
     * Sanitize incoming data attributes.
     *
     * @return array
     */
    public function sanitizable() :array;
}
