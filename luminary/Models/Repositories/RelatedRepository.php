<?php

namespace Luminary\Models\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Luminary\Contracts\Entity\RelatedRepository as BaseRelatedRepository;
use Luminary\Database\Eloquent\Relations\ManageRelations;
use Luminary\Services\ApiRequest\RequestRepositoryTrait;

class RelatedRepository implements BaseRelatedRepository
{
    use ManageRelations;
    use RequestRepositoryTrait;

    /**
     * Retrieve all relationship records
     *
     * @param $parentId
     * @param string $relationship
     * @return Collection
     */
    public static function all($parentId, string $relationship) :Collection
    {
        $model = static::builder(['archived']);

        $result = static::getRelationship($model, $parentId, $relationship, [], true);

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
        $model = static::builder(['archived'])->findOrFail($parentId);

        $query = function($query) use($relationshipId){
            if(!is_null($relationshipId)) {
                $query->whereId($relationshipId);
            }

            $query->getModel()->applyRelatedQueryScope($query);
        };

        $result = static::getModelRelationship($relationship, $model, $query)->first();

        return $result ?: static::getEmptyModel($model->{$relationship}());;
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
        $parent = static::builder()->findOrFail($parentId);

        $model = static::getModelRelationship($relationship, $parent)->getModel()->create($data);

        if($model->id) {
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
        $parent = static::builder()->findOrFail($parentId);

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
     * @throws \Exception
     */
    public static function delete($parentId, string $relationship, $relationshipId) :bool
    {
        $parent = static::builder()->findOrFail($parentId);

        return static::getModelRelationship($relationship, $parent)->find($relationshipId)->delete();
    }
}
