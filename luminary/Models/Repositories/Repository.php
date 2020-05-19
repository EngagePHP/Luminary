<?php

namespace Luminary\Models\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Luminary\Contracts\Entity\Repository as RepositoryContract;
use Luminary\Database\Eloquent\Relations\ManageRelations;
use Luminary\Services\ApiRequest\RequestRepositoryTrait;

class Repository implements RepositoryContract
{
    use ManageRelations;
    use RequestRepositoryTrait;

    /**
     * Retrieve all records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all() :Collection
    {
        return static::builder([], true)->getModel()->all();
    }

    /**
     * Find a record by id
     *
     * @param $id
     * @param array $without
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function find($id, $without = ['archived','expired']) :Model
    {
        return static::findAll((array) $id, $without)->first();
    }

    /**
     * Find multiple records by id
     *
     * @param array $ids
     * @param array $without
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findAll(array $ids, $without = []) :Collection
    {
        return static::builder($without, true)->find((array) $ids);
    }

    /**
     * Create a new record
     *
     * @param array $data
     * @param array $relationships ['relNameOne' => 9, 'relNameMany' => [1,2,3,4]]
     * @return Model
     */
    public static function create(array $data, array $relationships = []) :Model
    {
        $model = static::builder()->create($data);

        if(count($relationships)) {
            static::createRelationships($model, $relationships);
        }

        return $model;
    }

    /**
     * Create multiple new records with
     * an array of attribute arrays
     *
     * @param array $data
     * @return string|false
     */
    public static function createAll(array $data)
    {
        $now = Carbon::now()->toDateTimeString();

        $timestamps = [
            'created_at'=> $now,
            'updated_at'=> $now
        ];

        $data = collect($data)->transform(
            function ($attributes) use ($timestamps) {
                return array_merge($attributes, $timestamps);
            }
        )->all();

        $insert = static::builder()::insert($data);

        return $insert ? (string) $now : false;
    }

    /**
     * Update a record by id
     *
     * @param $id
     * @param array $data
     * @param array $relationships ['relNameOne' => 9, 'relNameMany' => [1,2,3,4]]
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function update($id, array $data, array $relationships = []) :Model
    {
        $model = static::find($id, $without = ['archived', 'trashed', 'expired']);

        $model->update($data);

        if(count($relationships)) {
            $relationships = static::fillMissingRelationships($model, $relationships);
            static::updateRelationships($model, $relationships);
        }

        return $model;
    }

    /**
     * Update a record by id
     *
     * @param array $ids
     * @param array $data
     * @return bool
     */
    public static function updateAll(array $ids, array $data) :bool
    {
        return static::builder(['archived', 'trashed','expired'], true)->whereIn('id', $ids)->update($data);
    }

    /**
     * Delete a record by id
     *
     * @param $id
     * @return bool
     */
    public static function delete($id) :bool
    {
        static::find($id)->delete();
        return true;
    }

    /**
     * Delete multiple records
     * by ID
     *
     * @param array $ids
     * @return bool
     */
    public static function deleteAll(array $ids) :bool
    {
        static::findAll($ids, ['archived','expired'])->each(function($model) {
            $model->delete();
        });

        return true;
    }
}
