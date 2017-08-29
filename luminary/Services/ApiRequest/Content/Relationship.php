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
        $relationships = collect($relationships)->map(
            function ($values) {
                $id = array_get($values, 'id');

                return is_null($id) ? array_pluck($values, 'id') : $id;
            }
        )->toArray();

        return $relationships;
    }
}
