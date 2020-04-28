<?php

namespace Luminary\Database\Eloquent\Relations;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Collection;

trait ManageRelations
{
    /**
     * Create multiple relationships with a model
     *
     * @param Model $model
     * @param array $relationships
     * @return void
     */
    public static function createRelationships(Model $model, $relationships = []) :void
    {
        static::manageRelationships($model, $relationships, 'create');
    }

    /**
     * Create a relatioship with a model
     *
     * @param Model $model
     * @param string $relationship
     * @param $value
     * @return void
     */
    public static function createRelationship(Model $model, string $relationship, $value) :void
    {
        static::manageRelationship($model, $relationship, $value, 'create');
    }

    /**
     * Update multiple relationships with a model
     *
     * @param Model $model
     * @param array $relationships
     * @return void
     */
    public static function updateRelationships(Model $model, $relationships = []) :void
    {
        static::manageRelationships($model, $relationships, 'update');
    }

    /**
     * Update a single relationship with a model
     *
     * @param Model $model
     * @param string $relationship
     * @param $value
     * @return void
     */
    public static function updateRelationship(Model $model, string $relationship, $value) :void
    {
        static::manageRelationship($model, $relationship, $value, 'update');
    }

    /**
     * Delete multiple relationships with a model
     *
     * @param Model $model
     * @param array $relationships
     * @return void
     */
    public static function deleteRelationships(Model $model, $relationships = []) :void
    {
        static::manageRelationships($model, $relationships, 'delete');
    }

    /**
     * Delete a single relationship with a model
     *
     * @param Model $model
     * @param string $relationship
     * @param $value
     * @return void
     */
    public static function deleteRelationship(Model $model, string $relationship, $value) :void
    {
        static::manageRelationship($model, $relationship, $value, 'delete');
    }

    /**
     * Manage multiple relationships for a model
     *
     * @param Model $model
     * @param array $relationships
     * @param string $action
     * @return void
     */
    protected static function manageRelationships(
        Model $model,
        array $relationships = [],
        string $action = 'create'
    ) :void {
        collect($relationships)->each(
            function ($value, $key) use ($model, $action) {
                static::manageRelationship($model, $key, $value, $action, false, false);
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
     * @param string $action
     * @param bool $save
     * @param bool $load
     * @return void
     */
    protected static function manageRelationship(
        Model $model,
        string $relationship,
        $value,
        string $action,
        bool $save = true,
        bool $load = true
    ) :void {
        $relation = static::getModelRelationship($relationship, $model);
        $method = static::getRelationMethod($relation, $action);
        $models = static::getRelationMethodValuesByInput($value, $method, $relation);

        if (method_exists(new static, $method)) {
            static::$method($relation, $models);
        } else {
            $relation->{$method}($models);
        }

        !$save ?: $model->save();
        !$load ?: $model->load($relationship);
    }

    /**
     * Sync related ids from a HasMany relationship
     *
     * @param Relation $relation
     * @param array $ids
     * @return void
     */
    protected static function syncMany(Relation $relation, array $ids) :void
    {
        $relation->whereNotIn('id', $ids)->delete();
    }

    /**
     * Remove related items from a HasMany Relationship
     *
     * @param Relation $relation
     * @param array $ids
     * @return void
     */
    protected static function detachMany(Relation $relation, array $ids) :void
    {
        $relation->whereIn('id', $ids)->delete();
    }

    /**
     * Get the related models based on
     * passed value
     *
     * @param mixed $input
     * @return array|Model
     */
    private static function getRelationMethodValuesByInput($input, $method, Relation $relation)
    {
        $modelSave = in_array($method, ['save', 'saveMany']);

        // Return only id's for sync method
        if (!$modelSave && $input instanceof Collection) {
            $input = $input->pluck('id');
        }

        // Return Models for any method that is not sync and is an id or list of ids
        if ($modelSave  && ! $input instanceof Collection && ! $input instanceof Model) {
            $input = array_keys($input);
            $input = static::getRelatedModels($relation, $input);
        }

        // SaveMany method requires an array of Models
        if ($method == 'saveMany' && $input instanceof Model) {
            $input = [$input];
        }

        return $input;
    }

    /**
     * Get the relationship method
     * from the model
     *
     * @param $relationship
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param Closure $closure
     * @return Relation
     */
    public static function getModelRelationship($relationship, Model $model, Closure $closure = null) :Relation
    {
        if (! method_exists($model, $relationship)) {
            $message = "The $relationship relationship was not found";
            throw new RelationshipNotFoundException($relationship, $message);
        }

        $relation = $model->{$relationship}();

        if ($closure instanceof Closure) {
            $closure($relation);
        }

        return $relation;
    }

    /**
     * Get the related models by id
     *
     * @param Relation $relation
     * @param int|string $id
     * @return Model
     */
    public static function getRelatedModel(Relation $relation, $id)
    {
        $model = $relation->getModel()->find($id);

        if ($model instanceof Collection) {
            $model = $model->first();
        }

        return $model;
    }

    /**
     * Get the related models by id
     *
     * @param Relation $relation
     * @param int|array|string $ids
     * @return array|Model
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
     * @param $action
     * @return string
     */
    public static function getRelationMethod(Relation $relation, $action) :string
    {
        switch ($action) {
            case 'delete':
                return static::getRelationDeleteMethod($relation);
                break;
            case 'update':
                return static::getRelationUpdateMethod($relation);
                break;
            case 'create':
            default:
                return static::getRelationCreateMethod($relation);
        }
    }

    /**
     * Get the create method name by relation
     *
     * @param Relation $relation
     * @return string
     */
    public static function getRelationCreateMethod(Relation $relation) :string
    {
        switch (true) {
            case $relation instanceof HasOne:
            case $relation instanceof MorphOne:
                return 'save';
                break;
            case $relation instanceof BelongsToMany:
            case $relation instanceof HasMany:
            case $relation instanceof MorphMany:
                return 'saveMany';
                break;

            case $relation instanceof BelongsTo:
            case $relation instanceof MorphTo:
                return 'associate';
                break;
        }
    }

    /**
     * Get the update method name by relation
     *
     * @param Relation $relation
     * @return string
     */
    public static function getRelationUpdateMethod(Relation $relation) :string
    {
        switch (true) {
            case $relation instanceof HasOne:
            case $relation instanceof MorphOne:
                return 'save';
                break;
            case $relation instanceof BelongsToMany:
                return 'sync';
                break;
            case $relation instanceof HasMany:
            case $relation instanceof MorphMany:
                return 'syncMany';
                break;
            case $relation instanceof BelongsTo:
            case $relation instanceof MorphTo:
                return 'associate';
                break;
        }
    }

    /**
     * Get the delete method name by relation
     *
     * @param Relation $relation
     * @return string
     */
    public static function getRelationDeleteMethod(Relation $relation) :string
    {
        switch (true) {
            case $relation instanceof BelongsToMany:
                return 'detach';
                break;
            case $relation instanceof HasOne:
            case $relation instanceof MorphOne:
            case $relation instanceof HasMany:
            case $relation instanceof MorphMany:
                return 'detachMany';
                break;
            case $relation instanceof BelongsTo:
            case $relation instanceof MorphTo:
                return 'dissociate';
                break;
        }
    }

    /**
     * Get the foreign key for a Model relationship
     *
     * @param $relationship
     * @param Model $model
     * @return null|string
     */
    public static function getForeignKey($relationship, Model $model)
    {
        $relation = static::getModelRelationship($relationship, $model);

        switch (true) {
            case $relation instanceof BelongsTo:
            case $relation instanceof MorphTo:
                return str_singular($relationship) . '_id';
                break;
            default:
                return null;
        }
    }

    /**
     * Query a Models Relationship
     *
     * @param Model $model
     * @param $id
     * @param string $relationship
     * @param array $columns
     * @return Collection|Model|null
     */
    public static function getRelationship($model, $id, string $relationship, array $columns = [])
    {
        if($model instanceof Builder) {
            return static::getBuilderRelationship($model, $id, $relationship, $columns);
        }

        $primaryKey = $model->getQualifiedKeyName();
        $get = array_filter(['id', static::getForeignKey($relationship, $model)]);

        $results = $model->with([
            $relationship => function ($query) use ($model, $columns) {
                empty($columns) ?: static::addQuerySelect($model, $query, $columns);
            }
        ]);

        $results = $results->where($primaryKey, $id);
        $results = $results->first($get);

        return $results->getRelation($relationship);
    }

    /**
     * Query a Models Relationship
     *
     * @param Model $model
     * @param $id
     * @param string $relationship
     * @param array $columns
     * @return Collection|Model|null
     */
    public static function getBuilderRelationship(Builder $builder, $id, string $relationship, array $columns = [])
    {
        $relationClass = static::getModelClassByMorphName($relationship);
        $relationClass::applyApiQueryScope(null, $relationship);

        $get = array_filter(['id', static::getForeignKey($relationship, $builder->getModel())]);
        $model = $builder->find($id, $get);

        $results = $model->load([
            $relationship => function ($query) use ($model, $columns) {
                empty($columns) ?: static::addQuerySelect($model, $query, $columns);
            }
        ]);

        return $results->getRelation($relationship);
    }

    /**
     * Add a relationship query select statement
     * to a query builder instance
     *
     * @param Model $parent
     * @param Relation $query
     * @param $columns
     */
    public static function addQuerySelect(Model $parent, Relation $query, $columns)
    {
        if ($query instanceof BelongsToMany) {
            $table = $query->getModel()->getTable();

            if (array_search('id', $columns, true) === false) {
                $columns[] = 'id';
                $query->getModel()->addHidden('id');
            }

            $columns = array_map(function ($column) use ($table) {
                return $table . '.' . $column;
            }, $columns);
        } elseif ($query instanceof HasMany) {
            $queryColumn = str_singular($parent->getTable()) . '_id';

            if (array_search($queryColumn, $columns, true) === false) {
                $columns[] = $queryColumn;
                $query->getModel()->addHidden($queryColumn);
            }
        }

        $query->select($columns);
    }

    /**
     * Get an empty relationship
     *
     * @param $relationship
     * @param Relation $relation
     * @return Model|Collection
     */
    public static function getEmptyRelationship($relationship, Relation $relation)
    {
        return $relationship === str_singular($relationship)
            ? $relation->getModel()
            : new Collection;
    }

    /**
     * Return and empty Collection
     *
     * @return Collection
     */
    public static function getEmptyCollection()
    {
        return new Collection;
    }

    /**
     * Return an empty relation model
     *
     * @param Relation $relation
     * @return Model
     */
    public static function getEmptyModel(Relation $relation)
    {
        return $relation->getModel();
    }

    /**
     * Get the model class by morph name
     *
     * @param string $morphName
     * @return mixed
     */
    public static function getModelClassByMorphName(string $morphName)
    {
        $morphMap = Relation::morphMap();

        if($model = array_get($morphMap, $morphName)) {
            return $model;
        }
    }

    /**
     * Fill missing relationships for a resource update
     *
     * @param Model $model
     * @param array $relationships
     * @return array
     */
    public static function fillMissingRelationships(Model $model, $relationships)
    {
        return (new FillMissingRelationships($model, $relationships))->fill();
    }

    /**
     * Fill missing relationships for a resource update
     *
     * @param Model $model
     * @param array $relationships
     * @return array
     */
    public static function fillMissingRelationshipAttributes(Model $model, $relationships)
    {
        return (new FillMissingRelationshipAttributes($model, $relationships))->fill();
    }
}
