<?php

namespace Luminary\Concerns;

use Laravel\Lumen\Concerns\RoutesRequests as LumenRoutesRequests;

trait RoutesRequests
{
    use LumenRoutesRequests;

    /**
     * All of the resource specific authorizers
     *
     * @var array
     */
    protected $authorizers = [];

    /**
     * All of the resource specific sanitizers
     *
     * @var array
     */
    protected $sanitizers = [];

    /**
     * All of the resource specific validators
     *
     * @var array
     */
    protected $validators = [];

    /**
     * Add an authenticator by resource
     *
     * @param string $resource
     * @param string $authorizer
     * @return $this
     */
    public function authorizers(string $resource, string $authorizer)
    {
        $this->authorizers[$resource] = $authorizer;

        return $this;
    }

    /**
     * Get a resource authenticator
     *
     * @param string $resource
     * @param null $default
     * @return null|string
     */
    public function authorizer(string $resource, $default = null)
    {
        return array_get($this->authorizers, $resource, $default);
    }

    /**
     * Add a sanitizer by resource
     *
     * @param string $resource
     * @param string $sanitizer
     * @return $this
     */
    public function sanitizers(string $resource, string $sanitizer)
    {
        $this->sanitizers[$resource] = $sanitizer;

        return $this;
    }

    /**
     * Get a resource sanitizer
     *
     * @param string $resource
     * @param null $default
     * @return null|string
     */
    public function sanitizer(string $resource, $default = null)
    {
        return array_get($this->sanitizers, $resource, $default);
    }

    /**
     * Add a validator type by resource
     *
     * @param string $resource
     * @param string $type
     * @param string $validator
     */
    public function validators(string $resource, string $type, string $validator)
    {
        $validators = array_get($this->validators, $resource, []);
        $validators[$type] = $validator;

        $this->validators[$resource] = $validators;
    }

    /**
     * Get a validator type by resource
     *
     * @param string $resource
     * @param string $type
     * @param null $default
     * @return null|string
     */
    public function validator(string $resource, string $type, $default = null)
    {
        return array_get($this->validators, $resource . '.' . $type, $default);
    }
}
