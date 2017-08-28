<?php

namespace Luminary\Contracts\Entity;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RelationshipRepository
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
     * @return Model
     */
    public static function find($parentId, string $relationship) :Model;

    /**
     * Create a new relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param string|int|array $id
     * @return bool
     */
    public static function create($parentId, string $relationship, $id) :bool;

    /**
     * Update a relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param int|string|array $id
     * @return bool
     */
    public static function update($parentId, string $relationship, $id) :bool;

    /**
     * Delete a relationship record
     *
     * @param $parentId
     * @param string $relationship
     * @param null|int|string|array $id
     * @return bool
     */
    public static function delete($parentId, string $relationship, $id = null) :bool;
}
