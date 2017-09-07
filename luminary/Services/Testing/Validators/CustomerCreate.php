<?php

namespace Luminary\Services\Testing\Validators;

use Luminary\Services\Validation\Contracts\ValidatorArguments;

class CustomerCreate implements ValidatorArguments
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() :array
    {
        return [
            //
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() :array
    {
        return [
            //
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() :array
    {
        return [
            //
        ];
    }

    /**
     * Sanitize the data values from the request.
     *
     * @return array
     */
    public function sanitize() :array
    {
        return [
            //
        ];
    }
}
