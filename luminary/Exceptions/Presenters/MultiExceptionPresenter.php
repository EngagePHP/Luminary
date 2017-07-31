<?php

namespace Luminary\Exceptions\Presenters;

use Luminary\Exceptions\MultiException;

class MultiExceptionPresenter extends HttpExceptionPresenter
{
    /**
     * The Exception instance
     *
     * @var MultiException
     */
    protected $exception;

    /**
     * Return the error response array
     *
     * @return array
     */
    public function response() :array
    {
        return array_map(
            function ($e) {
                $response = (new HttpExceptionPresenter($e))->response();

                return head($response);
            },
            $this->exception->exceptions()
        );
    }
}
