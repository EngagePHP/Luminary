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
