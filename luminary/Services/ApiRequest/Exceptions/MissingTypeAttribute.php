<?php

namespace Luminary\Services\ApiRequest\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MissingTypeAttribute extends MissingDataAttribute
{
    /**
     * Set the response pointer source
     *
     * @return array
     */
    public function getSource()
    {
        return [
            'pointer' => '/data/type'
        ];
    }
}
