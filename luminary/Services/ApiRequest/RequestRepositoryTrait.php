<?php

namespace Luminary\Services\ApiRequest;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Luminary\Models\Archive\ArchiveModelScope;
use Luminary\Models\Expire\ExpireModelScope;
use Luminary\Services\ApiQuery\QueryRepositoryTrait;

trait RequestRepositoryTrait
{
    use QueryRepositoryTrait;

    /**
     * Get the request resource model
     *
     * @param array $without
     * @param bool $applyQuery
     * @return mixed
     */
    static public function builder(array $without = [], bool $applyQuery = false): Builder
    {
        try {
            static::clearBootedModels();
            $modelClass = static::getModelClass();

            if($applyQuery) {
                static::query($modelClass);
            }

            return count($without)
                ? static::getWithoutModel($modelClass, $without)
                : static::getModel($modelClass);

        } catch (\Exception $e) {
            throw new ModelNotFoundException('resource entity not found');
        }
    }

    /**
     * Clear the any models already booted
     * in the application
     */
    static public function clearBootedModels()
    {
        Model::clearBootedModels();
    }

    /**
     * Get the model class namespace
     *
     * @return mixed
     */
    static public function getModelClass()
    {
        $resource = app('request')->parentResource();
        $morphMap = Relation::morphMap();

        if($model = array_get($morphMap, $resource)) {
            return $model;
        }
    }

    /**
     * Get a new instance of the model
     *
     * @param string $class
     * @return Builder
     */
    static public function getModel(string $class): Builder
    {
        return (new $class)->newQuery();
    }

    /**
     * Get a new instance of the
     * model without mapped global
     * scopes
     *
     * @param string $class
     * @param array $without
     * @return Model
     */
    static public function getWithoutModel(string $class, array $without): Builder
    {
        $without = static::mapWithout($without);
        return $class::withoutGlobalScopes($without);
    }

    /**
     * Map the without request
     *
     * @param $without
     * @return array
     */
    static public function mapWithout($without): array
    {
        $without = array_only([
            'archived' => ArchiveModelScope::class,
            'trashed' => SoftDeletingScope::class,
            'expired' => ExpireModelScope::class
        ], $without);

        return array_values($without);
    }
}
