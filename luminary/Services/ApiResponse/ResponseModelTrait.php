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
     * The pivot meta keys to return
     *
     * @var array
     */
    protected $pivotMeta = [];

    /**
     * The default response type
     * Useful if type needs to be
     * different from the table
     *
     * @var array
     */
    protected $type;

    /**
     * Return the meta data
     *
     * @return array
     */
    public function meta() :array
    {
        return array_merge(
            $this->modelMeta(),
            $this->pivotMeta()
        );
    }

    /**
     * Get the model meta to return
     *
     * @return array
     */
    public function modelMeta() :array
    {
        return collect($this->meta)->flip()->map(
            function ($key, $meta) {
                return $this->getAttributeValue($meta);
            }
        )->toArray();
    }

    /**
     * Get the pivot meta to return
     * if exists
     *
     * @return array
     */
    public function pivotMeta() :array
    {
        if(!method_exists($this->pivot, 'getPivotMetaKeys')) {
            return [];
        }

        return collect($this->pivot->getPivotMetaKeys())->flip()->map(
            function ($key, $meta) {
                return $this->pivot->getAttributeValue($meta);
            }
        )->toArray();
    }

    /**
     * Get the pivot meta key
     * array
     *
     * @return array
     */
    public function getPivotMetaKeys() :array
    {
        return $this->pivotMeta;
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
        $query = $this->query();

        if($query->isPaginated($this) && $collection = $query->getPaginatedCollection()) {
            return $collection;
        }

        return new Collection($models);
    }
}
