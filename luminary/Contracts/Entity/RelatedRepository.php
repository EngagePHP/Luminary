<?php

namespace Luminary\Contracts\Entity;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RelatedRepository
{
    /**
     * Retrieve all relationship records
     *
     * @param $parentId
     * @param string $relationship
     * @return Collection
     */
    public static function all($parentId, string $relationship) :Collection;

    /**
     * Find a specific relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param null $relationshipId
     * @return Model
     */
    public static function find($parentId, string $relationship, $relationshipId = null) :Model;

    /**
     * Create a new relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param array $data
     * @return Model
     */
    public static function create($parentId, string $relationship, array $data) :Model;

    /**
     * Update a relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param $relationshipId
     * @param array $data
     * @return Model
     */
    public static function update($parentId, string $relationship, $relationshipId, array $data) :Model;

    /**
     * Delete a relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param $relationshipId
     * @return bool
     */
    public static function delete($parentId, string $relationship, $relationshipId) :bool;
}
