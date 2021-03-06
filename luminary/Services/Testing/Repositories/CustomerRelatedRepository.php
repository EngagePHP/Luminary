<?php

namespace Luminary\Services\Testing\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Luminary\Contracts\Entity\RelatedRepository;
use Luminary\Database\Eloquent\Relations\ManageRelations;
use Luminary\Services\Testing\Models\Customer;

class CustomerRelatedRepository implements RelatedRepository
{
    use ManageRelations;

    /**
     * Retrieve all relationship records
     *
     * @param $parentId
     * @param string $relationship
     * @return Collection
     */
    public static function all($parentId, string $relationship) :Collection
    {
        $model = new Customer;
        $result = static::getRelationship($model, $parentId, $relationship);

        return $result ?: static::getEmptyRelationship($relationship, $model->{$relationship}());
    }

    /**
     * Find a specific relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param null $relationshipId
     * @return Model
     */
    public static function find($parentId, string $relationship, $relationshipId = null) :Model
    {
        $model = Customer::findOrFail($parentId);

        $query = function ($query) use ($relationshipId) {
            if (!is_null($relationshipId)) {
                $query->whereId($relationshipId);
            }
        };

        $result = static::getModelRelationship($relationship, $model, $query)->first();

        return $result ?: static::getEmptyModel($model->{$relationship}());
    }

    /**
     * Create a new relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param array $data
     * @return Model
     */
    public static function create($parentId, string $relationship, array $data) :Model
    {
        $parent = Customer::findOrFail($parentId);

        $model = static::getModelRelationship($relationship, $parent)->getModel()->create($data);

        if ($model->id) {
            static::createRelationships($parent, [$relationship => $model]);
        }

        return $model;
    }

    /**
     * Update a relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param $relationshipId
     * @param array $data
     * @return Model
     */
    public static function update($parentId, string $relationship, $relationshipId, array $data) :Model
    {
        $parent = Customer::findOrFail($parentId);

        $model = static::getModelRelationship($relationship, $parent)->find($relationshipId);

        $model->update($data);

        return $model;
    }

    /**
     * Delete a relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param $relationshipId
     * @return bool
     */
    public static function delete($parentId, string $relationship, $relationshipId) :bool
    {
        $parent = Customer::findOrFail($parentId);

        return static::getModelRelationship($relationship, $parent)->find($relationshipId)->delete();
    }
}
