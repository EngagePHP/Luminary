<?php

namespace Luminary\Services\ApiResponse;

use Illuminate\Database\Eloquent\Model;
use Luminary\Database\Eloquent\Collection;

trait ResponseModelTrait
{
    /**
     * The default meta keys to return
     *
     * @var array
     */
    protected $meta = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The default response type
     * Useful if type needs to be
     * different from the table
     *
     * @var array“lo;]
     */
    protected $type;

    /**
     * Return the meta data
     *
     * @return array
     */
    public function meta() :array
    {
        return collect($this->meta)->flip()->map(
            function ($key, $meta) {
                return $this->getAttributeValue($meta);
            }
        )->toArray();
    }

    /**
     * Set the attribute keys to return as meta data
     *
     * @param array $keys
     * @return Model
     */
    public function setMetaKeys(array $keys) :Model
    {
        $this->meta = $keys;

        return $this;
    }

    /**
     * Return the default response type
     * Will return table name if type
     * property is null
     *
     * @return string
     */
    public function getType() :string
    {
        return str_slug($this->type ?: $this->getTable());
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }
}
