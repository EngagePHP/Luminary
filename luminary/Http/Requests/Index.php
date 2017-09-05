<?php

namespace Luminary\Http\Requests;

use Luminary\Services\FormRequests\AuthRequest;

class Index extends AuthRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() :bool
    {
        return $this->authorize->list();
    }
}
