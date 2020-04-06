<?php

namespace Luminary\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FillMissingRelationshipAttributes
{
    /**
     * The Parent Model
     *
     * @var Model
     */
    protected $model;

    /**
     * The request relationships
     * to update
     *
     * @var array
     */
    protected $relationships;

    /**
     * FillMissingRelationshipAttributes constructor.
     * @param Model $model
     * @param array $relationships
     */
    public function __construct(Model $model, array $relationships)
    {
        $this->model = $model;
        $this->relationships = $relationships;
    }

    /**
     * Fill missing relationships for a resource update
     *
     * @return array
     */
    public function fill()
    {
        $relationships = $this->relationships;
        $modelRelations = $this->loadRequiredRelationships();

        return collect($relationships)->transform(function($relations, $key) use($modelRelations) {
            $merge = array_get($modelRelations, $key);
            return $merge ? $this->mergeRelations($merge, $relations) : $relations;
        })->all();
    }

    /**
     * Load the required relationships
     *
     * @return array
     */
    protected function loadRequiredRelationships()
    {
        $model = $this->model;
        $relationships = $this->relationships;
        $relations = $this->filterRelationsToLoad($relationships);

        $model->load($relations);
        $relations = $model->getRelations();
        $model->setRelations([]);

        return $relations;
    }

    /**
     * Filter the relationships
     * to load
     *
     * @param array $relationships
     * @return mixed
     */
    protected function filterRelationsToLoad(array $relationships)
    {
        $load = collect($relationships)
            ->keys()
            ->filter(function($relationship) {
                return str_singular($relationship) !== $relationship;
            })->all();

        return $load;
    }

    /**
     * Merge the relationships
     *
     * @param Collection $modelRelations
     * @param array $relations
     * @return array
     */
    protected function mergeRelations(Collection $modelRelations, array $relations)
    {
        $modelRelations = $this->formatModelRelations($modelRelations, $relations);
        $modelRelations = array_only($modelRelations, array_keys($relations));
        return array_replace_recursive($modelRelations, $relations);
    }

    /**
     * Format the model relationships
     * to match the request
     *
     * @param Collection $modelRelations
     * @param array $relations
     * @return array
     */
    protected function formatModelRelations(Collection $modelRelations, array $relations)
    {
        $attributes = $this->sharedAttributes($relations);

        return $modelRelations->keyBy('id')->mapWithKeys(function(Model $model, $key) use($attributes) {
            return [
              $key => $model->pivot ? $model->pivot->only($attributes) : $model->only($attributes)
            ];
        })->all();
    }

    /**
     * Get the relationship shared attributes
     *
     * @param array $relations
     * @return array
     */
    protected function sharedAttributes(array $relations): array
    {
        $keys = collect();

        collect($relations)->each(function($relation) use(&$keys){
            $relationKeys = array_keys($relation);
            $keys = $keys->merge($relationKeys);
        });

        return $keys->unique()->all();
    }
}
