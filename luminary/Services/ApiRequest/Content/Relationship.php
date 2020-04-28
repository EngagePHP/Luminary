<?php

namespace Luminary\Services\ApiRequest\Content;

class Relationship extends Related
{
    /**
     * Get the attributes from an request
     *
     * @return array
     */
    public function attributes() :array
    {
        $attributes = array_get($this->content, 'data.attributes', []);

        if ($id = array_get($this->content, 'data.id')) {
            $attributes = array_add($attributes, 'id', $id);
        }

        return $attributes;
    }

    /**
     * Set the relationships from a request
     *
     * @return array
     */
    public function relationships() :array
    {
        $relationships = array_get($this->content, 'data', []) ?: [];

        if ($id = array_get($relationships, 'id')) {
            return [$id];
        }

        $relationships = collect($relationships)->mapWithKeys(
            function ($value) {
                return [
                    array_get($value, 'id') => array_get($value, 'attributes', [])
                ];
            }
        )->toArray();

        return $relationships;
    }
}
