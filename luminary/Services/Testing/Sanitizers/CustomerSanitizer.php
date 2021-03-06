<?php

namespace Luminary\Services\Testing\Sanitizers;

use Luminary\Services\Sanitation\Contracts\SanitizerArguments;

class CustomerSanitizer implements SanitizerArguments
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
