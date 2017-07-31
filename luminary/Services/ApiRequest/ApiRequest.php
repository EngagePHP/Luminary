<?php

namespace Luminary\Services\ApiRequest;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class ApiRequest extends Request
{
    /**
     * post relationship params
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $relationships;

    /**
     * request post type
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $type;

    /**
     * Get the attributes from an request
     *
     * @param array $content
     */
    public function setAttributesFromContent(array $content)
    {
        $attributes = array_get($content, 'data.attributes', []);

        if ($id = array_get($content, 'data.id')) {
            $attributes = array_add($attributes, 'id', $id);
        }

        $this->json = new ParameterBag($attributes);
    }

    /**
     * The the relationships property
     *
     * @return array
     */
    public function getRelationships()
    {
        return $this->relationships ? $this->relationships->all() : [];
    }

    /**
     * Set the relationships property
     *
     * @param array $relationships
     */
    public function setRelationships(array $relationships)
    {
        $this->relationships = new ParameterBag($relationships);
    }

    /**
     * Set the relationships from a request
     *
     * @param array $content
     */
    public function setRelationshipsFromContent(array $content)
    {
        $relationships = array_get($content, 'data.relationships', []) ?: [];
        $relationships = collect($relationships)->map(
            function ($values) {
                $data = array_get($values, 'data');
                $id = array_get($data, 'id');

                return is_null($id) ? array_pluck($data, 'id') : $id;
            }
        )->toArray();

        $this->setRelationships($relationships);
    }

    /**
     * Get the document type parameter
     *
     * @return ParameterBag
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the document type parameter
     *
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * Set the document type from request
     *
     * @param array $content
     */
    public function setTypeFromContent(array $content)
    {
        $type = array_get($content, 'data.type', '');
        $this->setType($type);
    }
}
