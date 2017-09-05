<?php

namespace Luminary\Services\Validation\Contracts;

interface ValidatorArguments
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() :array;

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() :array;

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() :array;
}
