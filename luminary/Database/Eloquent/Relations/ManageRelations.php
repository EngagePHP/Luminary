<?php

namespace Luminary\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

trait ManageRelations
{
    /**
     * Manage multiple relationships for a model
     *
     * @param Model $model
     * @param array $relationships
     */
    public static function manageRelationships(Model $model, $relationships = [])
    {
        collect($relationships)->each(
            function ($value, $key) use ($model) {
                static::manageRelationship($model, $key, $value, false, false);
            }
        );

        $model->save();
        $model->load(...array_keys($relationships));
    }

    /**
     * Manage a model relationship by
     * name and value
     *
     * @param Model $model
     * @param string $relationship
     * @param $value
     * @param bool $save
     * @param bool $load
     */
    public static function manageRelationship(
        Model $model,
        string $relationship,
        $value,
        bool $save = true,
        bool $load = true
    ) {
        $relation = static::getModelRelationship($relationship, $model);
        $method = static::getRelationMethod($relation);

        if ($method !== 'sync') {
            $value = static::getRelatedModels($relation, $value);
        }

        $relation->{$method}($value);

        !$save ?: $model->save();
        !$load ?: $model->load($relationship);
    }

    /**
     * Get the relationship method
     * from the model
     *
     * @param $relationship
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public static function getModelRelationship($relationship, Model $model) :Relation
    {
        if (! method_exists($model, $relationship)) {
            $message = "The $relationship relationship was not found";
            throw new RelationshipNotFoundException($relationship, $message);
        }

        return $model->{$relationship}();
    }

    /**
     * Get the related models by id
     *
     * @param Relation $relation
     * @param int|array|string $ids
     * @return \Illuminate\Database\Eloquent\Model | array
     */
    public static function getRelatedModels(Relation $relation, $ids)
    {
        $models = $relation->getModel()->find($ids);

        if ($models instanceof Collection) {
            $models = $models->all();
        }

        return $models;
    }

    /**
     * Get the method name for a relation insert
     *
     * @param Relation $relation
     * @return string
     */
    public static function getRelationMethod(Relation $relation) :string
    {
        switch (true) {
            case $relation instanceof HasOne:
            case $relation instanceof MorphOne:
                return 'save';
                break;
            case $relation instanceof HasMany:
            case $relation instanceof MorphMany:
                return 'saveMany';
                break;

            case $relation instanceof BelongsTo:
            case $relation instanceof MorphTo:
                return 'associate';
                break;
            case $relation instanceof BelongsToMany:
                return 'sync';
                break;
        }
    }
}
