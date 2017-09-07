<?php

namespace Luminary\Services\Sanitation;

use Luminary\Services\Sanitation\Contracts\SanitizerArguments;

class DefaultSanitizable implements SanitizerArguments
{
    /**
     * Sanitize the data values from the request.
     *
     * @return array
     */
    public function sanitizable() :array
    {
        return [
            //
        ];
    }
}
