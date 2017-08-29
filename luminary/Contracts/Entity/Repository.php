<?php

namespace Luminary\Contracts\Entity;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface Repository
{
    /**
     * Retrieve all records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all() :Collection;

    /**
     * Find a specific record
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function find($id) :Model;

    /**
     * Find multiple records by id
     *
     * @param $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findAll(array $ids) :Collection;

    /**
     * Create a new record
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function create(array $data) :Model;

    /**
     * Create multiple new records
     *
     * @param array $data
     * @return bool
     */
    public static function createAll(array $data);

    /**
     * Update a record
     *
     * @param $id
     * @param array $data
     * @return Model
     */
    public static function update($id, array $data) :Model;

    /**
     * Update a record by id
     *
     * @param array $ids
     * @param array $data
     * @return bool
     */
    public static function updateAll(array $ids, array $data) :bool;

    /**
     * Delete a record
     *
     * @param $id
     * @return bool
     */
    public static function delete($id) :bool;

    /**
     * Delete multiple records
     * by ID
     *
     * @param array $ids
     * @return bool
     */
    public static function deleteAll(array $ids) :bool;
}
