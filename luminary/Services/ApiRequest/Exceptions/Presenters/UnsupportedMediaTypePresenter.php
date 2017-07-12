<?php

namespace Luminary\Services\ApiRequest\Exceptions\Presenters;

use Luminary\Exceptions\Presenters\HttpExceptionPresenter;

class UnsupportedMediaTypePresenter extends HttpExceptionPresenter
{
    /**
     * Return the error response title
     *
     * @return string
     */
    public function title() :string
    {
        return 'An unsupported Media Type was detected';
    }

    /**
     * Return the error response array
     *
     * @return array
     */
    public function response() :array
    {
        return [
            [
                'status' => $this->status(),
                'title' => $this->title(),
                'detail' => $this->message()
            ]
        ];
    }
}
