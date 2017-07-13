<?php

namespace Luminary\Services\ApiResponse\Serializers;

interface SerializerInterface
{
    /**
     * Return the data property
     *
     * @return array
     */
    public function data() :array;

    /**
     * Set/Replace the data property
     *
     * @param array $data
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setData(array $data) :AbstractSerializer;

    /**
     * Parse the model and fill
     * the class attributes
     *
     * @param mixed $items
     */
    public function fill($items) :void;

    /**
     * Get the included property
     *
     * @return array
     */
    public function included() :array;

    /**
     * Set/Replaced the included property
     *
     * @param array $included
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setIncluded(array $included) :AbstractSerializer;

    /**
     * And an included array to the
     * existing included property
     *
     * @param array $included
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function addIncluded(array $included) :AbstractSerializer;

    /**
     * Return the JsonApi Version
     *
     * @return string
     */
    public function jsonapi() :string;

    /**
     * Get the links for the
     * resource collection
     *
     * @return array
     */
    public function links() :array;

    /**
     * Return the meta property
     * as an array
     */
    public function meta() :array;

    /**
     * Get/Replace the meta property
     *
     * @param array $meta
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setMeta(array $meta = null) :AbstractSerializer;

    /**
     * Add an item to the meta property
     *
     * @param $key
     * @param $value
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function addMeta($key, $value) :AbstractSerializer;

    /**
     * Get the paginated property
     *
     * @return bool
     */
    public function paginated() :bool;

    /**
     * Set the paginated property
     *
     * @param bool $bool
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setPaginated(bool $bool) :AbstractSerializer;

    /**
     * Create an array of pagination links
     * if the Serializer is paginated
     *
     * @return array
     */
    public function paginationLinks() :array;

    /**
     * Get the resource property
     *
     * @return string
     */
    public function resource() :string;

    /**
     * Set the resource property
     *
     * @param null $resource
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setResource($resource = null) :AbstractSerializer;

    /**
     * Return the resource self link
     *
     * @return string
     */
    public function selfLink() :string;

    /**
     * Return the resource related link
     *
     * @return string
     */
    public function relatedLink() :string;

    /**
     * Get the resource property
     *
     * @return string
     */
    public function relationship() :string;

    /**
     * Set the resource property
     *
     * @param null $relationship
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setRelationship($relationship = null) :AbstractSerializer;

    /**
     * Return the serialized array
     *
     * @return array
     */
    public function serialize() :array;
}
