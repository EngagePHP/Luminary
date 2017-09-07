<?php

namespace Luminary\Services\Sanitation\Contracts;

interface SanitizesWhenResolved
{
    /**
     * Sanitize the request
     *
     * @return void
     */
    public function sanitizeInstance();
}
