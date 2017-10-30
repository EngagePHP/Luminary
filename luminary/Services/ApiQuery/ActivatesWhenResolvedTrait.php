<?php

namespace Luminary\Services\ApiQuery;

trait ActivatesWhenResolvedTrait
{
    /**
     * Authorize the class instance.
     *
     * @return void
     */
    public function activate()
    {
        app(Query::class)->activate();
    }
}
