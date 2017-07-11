<?php

namespace Luminary\Exceptions\Presenters;

class DefaultPresenter extends AbstractPresenter
{
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
