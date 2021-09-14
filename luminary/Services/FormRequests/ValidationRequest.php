<?php

namespace Luminary\Services\FormRequests;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Luminary\Services\Sanitation\Contracts\SanitizerArguments;
use Luminary\Services\Sanitation\Contracts\SanitizesWhenResolved;
use Luminary\Services\Sanitation\DefaultSanitizable;
use Luminary\Services\Validation\Contracts\ValidatorArguments;
use Luminary\Services\Validation\ValidatesWhenResolvedTrait;
use Luminary\Services\Sanitation\Traits\SanitizesWhenResolvedTrait;

class ValidationRequest extends AuthRequest implements SanitizesWhenResolved, ValidatesWhenResolved, ValidatorArguments
{
    use SanitizesWhenResolvedTrait;
    use ValidatesWhenResolvedTrait;

    /**
     * The type of validation for the request
     *
     * @var string
     */
    protected $validatorType;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() :bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() :array
    {
        return [];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() :array
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() :array
    {
        return [];
    }

    /**
     * Sanitize the data values from the request.
     *
     * @return array
     */
    public function sanitize() :array
    {
        return $this->getSanitizable()->sanitizable();
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance() :Validator
    {
        $factory = $this->container->make(ValidationFactory::class);

        if (method_exists($this, 'validator')) {
            $validator = $this->container->call([$this, 'validator'], compact('factory'));
        } else {
            $args = $this->validatorArgs();
            $validator = $this->createDefaultValidator($factory, $args);
        }

        if (method_exists($this, 'withValidator')) {
            $this->withValidator($validator);
        }

        return $validator;
    }

    /**
     * Create the default validator instance.
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @param \Luminary\Services\Validation\Contracts\ValidatorArguments $args
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function createDefaultValidator(ValidationFactory $factory, ValidatorArguments $args)
    {
        $data = $this->validationData();
        $ruleData = $data;
        $ruleData['id'] = array_get($data, 'id', 0);

        return $factory->make(
            $data,
            $this->container->call([$args, 'rules'], $ruleData),
            $args->messages(),
            $args->attributes()
        );
    }

    /**
     * Get the validator arguments class
     *
     * @return ValidatorArguments|ValidationRequest
     */
    public function validatorArgs()
    {
        $args = $this->isRelationship() ? null : app()->validator($this->resource(), $this->getValidatorType());
        return $args ? app($args) : $this;
    }

    /**
     * Get the validator type by method
     *
     * @return string
     */
    protected function getValidatorType()
    {
        switch ($this->method()) {
            case 'POST':
                return 'store';
                break;
            case 'PATCH':
                return 'update';
                break;
            default:
                return '';
        }
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        return $this->all();
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, $this->response(
            $this->formatErrors($validator)
        ));
    }

    /**
     * Get the sanitizable array
     *
     * @return SanitizerArguments
     */
    public function getSanitizable() :SanitizerArguments
    {
        $app = app();
        $resource = $this->isRelationship() ? '' : $this->resource();
        $class = $app->sanitizer($resource, DefaultSanitizable::class);

        return $app->make($class);
    }
}
