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
     * Get the document type parameter
     *
     * @return string
     */
    public function getType() :string
    {
        return $this->type;
    }

    /**
     * Set the document type parameter
     *
     * @param string $type
     * @return \Luminary\Services\ApiRequest\ApiRequest
     */
    public function setType(string $type) :ApiRequest
    {
        $this->type = $type;

        return $this;
    }
}
