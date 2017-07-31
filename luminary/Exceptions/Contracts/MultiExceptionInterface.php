<?php

namespace Luminary\Exceptions\Contracts;

interface MultiExceptionInterface
{
    /**
     * MultiExceptionInterface constructor.
     *
     * @param array $exceptions
     */
    public function __construct(array $exceptions);

    /**
     * Return the array of exceptions
     *
     * @return array
     */
    public function exceptions() :array;
}
