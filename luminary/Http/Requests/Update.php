<?php

namespace Luminary\Http\Requests;

use Luminary\Services\FormRequests\ValidationRequest;

class Update extends ValidationRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() :bool
    {
        return $this->authorize->create();
    }
}
