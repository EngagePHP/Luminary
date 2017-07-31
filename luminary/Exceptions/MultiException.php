<?php

namespace Luminary\Exceptions;

use Luminary\Exceptions\Contracts\MultiExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MultiException extends HttpException implements MultiExceptionInterface
{
    /**
     * The list of exceptions to render
     *
     * @var array
     */
    protected $exceptions;

    /**
     * MultiExceptionInterface constructor.
     *
     * @param array $exceptions
     * @param int $statusCode
     * @param null $message
     */
    public function __construct(array $exceptions, $statusCode = 400, $message = null)
    {
        $this->exceptions = $exceptions;

        parent::__construct($statusCode, $message);
    }

    /**
     * Return the array of exceptions
     *
     * @return array
     */
    public function exceptions() :array
    {
        return $this->exceptions;
    }
}
