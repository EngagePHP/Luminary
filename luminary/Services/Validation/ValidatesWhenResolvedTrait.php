<?php

namespace Luminary\Services\Validation;

use Illuminate\Validation\ValidatesWhenResolvedTrait as IlluminateValidatesWhenResolvedTrait;

trait ValidatesWhenResolvedTrait
{
    use IlluminateValidatesWhenResolvedTrait;

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        $this->prepareForValidation();

        $instance = $this->getValidatorInstance();

        if (! $instance->passes()) {
            $this->failedValidation($instance);
        }
    }
}
