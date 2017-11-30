<?php

namespace Luminary\Exceptions\Presenters;

class UnauthorizedPresenter extends DefaultPresenter
{
    /**
     * Return the http status code
     *
     * @return int
     */
    public function status() :int
    {
        return 401;
    }

    /**
     * Return the error response title
     *
     * @return null|string
     */
    public function title() :string
    {
        return 'Unauthorized';
    }
}
