<?php

namespace Luminary\Models\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Luminary\Contracts\Entity\RelationshipRepository as BaseRelationshipRepository;
use Luminary\Database\Eloquent\Relations\ManageRelations;
use Luminary\Services\ApiRequest\RequestRepositoryTrait;

class RelationshipRepository implements BaseRelationshipRepository
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
        $model = static::builder(['archived'])->getModel();
        $result = static::getRelationship($model, $parentId, $relationship, ['id']);

        return $result ?: static::getEmptyRelationship($relationship, $model->{$relationship}());
    }

    /**
     * Find a specific relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @return Model
     */
    public static function find($parentId, string $relationship) :Model
    {
        $model = static::builder(['archived'])->getModel();
        $result = static::getRelationship($model, $parentId, $relationship, ['id']);

        return $result ?: static::getEmptyModel($model->{$relationship}());
    }

    /**
     * Create a new relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param int|string|array $id
     * @return bool
     */
    public static function create($parentId, string $relationship, $id) :bool
    {
        $parent = static::builder()::findOrFail($parentId);

        static::createRelationships($parent, [$relationship => $id]);

        return true;
    }

    /**
     * Update a relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param int|string|array $id
     * @return bool
     */
    public static function update($parentId, string $relationship, $id) :bool
    {
        $parent = static::builder()::findOrFail($parentId);

        static::updateRelationships($parent, [$relationship => $id]);

        return true;
    }

    /**
     * Delete a relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param null $id
     * @return bool
     */
    public static function delete($parentId, string $relationship, $id = null) :bool
    {
        $parent = static::builder()::findOrFail($parentId);

        static::deleteRelationships($parent, [$relationship => $id]);

        return true;
    }
}
