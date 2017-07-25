<?php

namespace Luminary\Exceptions\Presenters;

class HttpExceptionPresenter extends DefaultPresenter
{
    /**
     * Return the http status code
     *
     * @return int
     */
    public function status() :int
    {
        return (int) $this->exception->getStatusCode();
    }
}
