<?php

namespace Luminary\Services\ApiRequest\Content;

class Content
{
    /**
     * The request body content
     *
     * @var array
     */
    protected $content;

    /**
     * Content constructor.
     *
     * @param array $content
     */
    public function __construct(array $content)
    {
        $this->content = $content;
    }

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
        $relationships = array_get($this->content, 'data.relationships', []) ?: [];
        $relationships = collect($relationships)->map(
            function ($values) {
                return collect(array_get($values, 'data'))->mapWithKeys(
                    function($value, $key) {
                        return [
                            array_get($value, 'id') => array_get($value, 'attributes', [])
                        ];
                    }
                )->all();
            }
        )->toArray();

        $keys = array_map(function($key) {
            return camel_case($key);
        }, array_keys($relationships));

        return array_combine($keys, array_values($relationships));
    }

    /**
     * Set the document type from request
     *
     * @return string
     */
    public function type() :string
    {
        return array_get($this->content, 'data.type', '');
    }
}
