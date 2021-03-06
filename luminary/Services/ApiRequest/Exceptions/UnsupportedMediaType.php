<?php

namespace Luminary\Services\ApiRequest\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnsupportedMediaType extends HttpException
{
    /**
     * The header where
     * the incorrect type was detected
     *
     * @var string
     */
    public $headerName;

    /**
     * UnsupportedMediaTypeException constructor
     *
     * @param string $headerName
     * @param null $message
     * @param \Exception|null $previous
     * @param int $code
     */
    public function __construct(string $headerName, $message = null, \Exception $previous = null, $code = 415)
    {
        parent::__construct(415, $message, $previous, array(), $code);
    }

    /**
     * Get the type property
     *
     * @return string
     */
    public function getHeaderName()
    {
        return $this->headerName;
    }
}
