<?php

namespace Luminary\Services\Testing\Requests;

use Luminary\Services\FormRequests\FormRequest as LuminaryFormRequest;

class FormRequest extends LuminaryFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
