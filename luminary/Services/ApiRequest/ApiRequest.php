<?php

namespace Luminary\Services\ApiRequest;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class ApiRequest extends Request
{
    /**
     * Parsed resource data
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $data;

    /**
     * Parsed relationship data
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $relationships;

    /**
     * Post Type
     *
     * @var string
     */
    public $type;

    /**
     * Parent Resource
     *
     * @var string
     */
    public $parentResource;

    /**
     * Resource
     *
     * @var string
     */
    public $resource;

    /**
     * is a related request
     *
     * @var bool
     */
    public $related = false;

    /**
     * Is a relationship request
     *
     * @var bool
     */
    public $relationship = false;

    /**
     * Get the data parameter bag
     *
     * @return array
     */
    public function data() :array
    {
        return $this->data ? $this->data->all() : [];
    }

    /**
     * Get the data parameter bag
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getData() :ParameterBag
    {
        return $this->data ?: new ParameterBag;
    }

    /**
     * Set the data parameter bag
     *
     * @param array $data
     * @return \Luminary\Services\ApiRequest\ApiRequest
     */
    public function setData(array $data) :ApiRequest
    {
        $this->data = new ParameterBag($data);

        return $this;
    }

    /**
     * The the relationships property
     *
     * @return array
     */
    public function relationships() :array
    {
        return $this->relationships ? $this->relationships->all() : [];
    }

    /**
     * The the relationships property
     *
     * @return @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getRelationships() :ParameterBag
    {
        return $this->relationships ?: new ParameterBag;
    }

    /**
     * Set the relationships property
     *
     * @param array $relationships
     * @return \Luminary\Services\ApiRequest\ApiRequest
     */
    public function setRelationships(array $relationships) :ApiRequest
    {
        $this->relationships = new ParameterBag($relationships);

        return $this;
    }

    /**
     * Alias for getType
     *
     * @return string
     */
    public function type() :string
    {
        return $this->getType();
    }

    /**
     * Get the document type parameter
     *
     * @return string
     */
    public function getType() :string
    {
        return $this->type ?: '';
    }

    /**
     * Set the document type parameter
     *
     * @param string $type
     * @return \Luminary\Services\ApiRequest\ApiRequest
     */
    public function setType(string $type) :ApiRequest
    {
        $type = str_replace('-', '_', $type);
        $this->type = $type;

        return $this;
    }

    /**
     * Alias for getResource
     *
     * @return string
     */
    public function resource() :string
    {
        return $this->getResource();
    }

    /**
     * Get the document resource
     *
     * @return string
     */
    public function getResource() :string
    {
        return $this->resource ?: '';
    }

    /**
     * Set the document type parameter
     *
     * @param string $resource
     * @return \Luminary\Services\ApiRequest\ApiRequest
     */
    public function setResource(string $resource) :ApiRequest
    {
        $resource = str_replace('_', '-', $resource);
        $this->resource = $resource;

        return $this;
    }

    /**
     * Alias for getResource
     *
     * @return string
     */
    public function parentResource() :string
    {
        return $this->getParentResource();
    }

    /**
     * Get the document resource
     *
     * @return string
     */
    public function getParentResource() :string
    {
        return $this->parentResource ?: '';
    }

    /**
     * Set the document type parameter
     *
     * @param string $resource
     * @return \Luminary\Services\ApiRequest\ApiRequest
     */
    public function setParentResource(string $resource) :ApiRequest
    {
        $resource = str_replace('_', '-', $resource);
        $this->parentResource = $resource;

        return $this;
    }

    /**
     * Get the document resource
     *
     * @return bool
     */
    public function isRelated() :bool
    {
        return $this->related;
    }

    /**
     * Set the document type parameter
     *
     * @param bool $bool
     * @return \Luminary\Services\ApiRequest\ApiRequest
     */
    public function setRelated(bool $bool = true) :ApiRequest
    {
        $this->related = $bool;

        return $this;
    }

    /**
     * Get the document resource
     *
     * @return bool
     */
    public function isRelationship() :bool
    {
        return $this->relationship;
    }

    /**
     * Set the document type parameter
     *
     * @param bool $bool
     * @return \Luminary\Services\ApiRequest\ApiRequest
     */
    public function setRelationship(bool $bool = true) :ApiRequest
    {
        $this->relationship = $bool;

        return $this;
    }
}
