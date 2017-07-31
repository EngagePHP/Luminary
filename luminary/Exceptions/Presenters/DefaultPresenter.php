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
        $response = [
            'status' => $this->status(),
            'title' => $this->title(),
            'detail' => $this->message(),
            'source' => $this->source()
        ];

        return [ array_filter($response) ];
    }
}
