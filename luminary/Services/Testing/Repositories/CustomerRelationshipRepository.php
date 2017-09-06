<?php

namespace Luminary\Services\Testing\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Luminary\Contracts\Entity\RelationshipRepository;
use Luminary\Database\Eloquent\Relations\ManageRelations;
use Luminary\Services\Testing\Models\Customer;

class CustomerRelationshipRepository implements RelationshipRepository
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
        $model = new Customer;
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
        $parent = Customer::findOrFail($parentId);

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
        $parent = Customer::findOrFail($parentId);

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
        $parent = Customer::findOrFail($parentId);

        static::deleteRelationships($parent, [$relationship => $id]);

        return true;
    }
}
