<?php

namespace Luminary\Services\ApiRequest\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MissingDataAttribute extends BadRequestHttpException
{
    /**
     * Set the response title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Bad Request';
    }

    /**
     * Set the response pointer source
     *
     * @return array
     */
    public function getSource()
    {
        return [
            'pointer' => '/data'
        ];
    }
}
