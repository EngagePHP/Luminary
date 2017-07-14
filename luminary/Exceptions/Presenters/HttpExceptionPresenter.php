<?php

namespace Luminary\Exceptions\Presenters;

class HttpExceptionPresenter extends DefaultPresenter
{
    /**
     * The Exception instance
     *
     * @var \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected $exception;

    /**
     * Return the http status code
     *
     * @return int
     */
    public function status() :int
    {
        return (int) $this->exception->getStatusCode();
    }

    /**
     * Return the error response title
     *
     * @return string
     */
    public function title() :string
    {
        return 'An internal server error has occurred';
    }
}
