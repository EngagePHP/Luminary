<?php

namespace Luminary\Services\ApiResponse\Presenters;

use Illuminate\Support\Collection;
use Luminary\Services\ApiResponse\ResponseHelper;
use Luminary\Services\ApiResponse\Serializers\ModelSerializer;

class RelationshipPresenter
{
    /**
     * The parent model instance
     *
     * @var \Luminary\Services\ApiResponse\Serializers\ModelSerializer
     */
    protected $parent;

    /**
     * The relationship collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $relationships;

    /**
     * PresenterInterface constructor
     *
     * @param \Luminary\Services\ApiResponse\Serializers\ModelSerializer $parent
     * @param \Illuminate\Support\Collection $relationships
     */
    public function __construct(ModelSerializer $parent, Collection $relationships)
    {
        $this->parent = $parent;
        $this->relationships = $relationships;
    }

    /**
     * Return the formatted presenter array
     *
     * @return array
     */
    public function format() :array
    {
        return $this->relationships->mapWithKeys(

            function ($models, $relationship) {
                $relationship = $this->formatRelationship($relationship);
                $models = $this->formatModels($models, $relationship);
                $links = $this->formatLinks($relationship);

                return [
                    $relationship => [
                        'links' => $links,
                        'data' => $models
                    ]
                ];
            }
        )->all();
    }

    /**
     * Return the relationship links array
     *
     * @param string $relationship
     * @return array
     */
    public function formatLinks(string $relationship) :array
    {
        $plural = $this->isPluralRelationship($relationship);

        return ResponseHelper::generateRelationshipLinks(
            $this->parent->type(),
            $this->parent->id(),
            $relationship,
            $plural
        );
    }

    /**
     * Format the models by model serializer
     *
     * @param Collection $models
     * @param string $relationship
     * @return array
     */
    public function formatModels(Collection $models, string $relationship) :array
    {
        $models = $models->map(
            function (ModelSerializer $model) {
                return $this->formatModel($model);
            }
        );

        // Return one model or null if relationship is singular
        if ($this->isSingularRelationship($relationship) && $models->count() <= 1) {
            return $models->first() ?: null;
        }

        return $models->all();
    }

    /**
     * Format the model as an array
     *
     * @return array
     */
    public function formatModel(ModelSerializer $model) :array
    {
        $type = $model->type();
        $id = $model->id();

        return compact('id', 'type');
    }

    /**
     * Format the relationship name
     *
     * @param string $relationship
     * @return string
     */
    public function formatRelationship(string $relationship) :string
    {
        $relationship = snake_case($relationship);
        return str_replace(['_', ' '], '-', $relationship);
    }

    /**
     * Is the relationship singular
     *
     * @param string $relationship
     * @return bool
     */
    public function isSingularRelationship(string $relationship) :bool
    {
        return str_singular($relationship) === $relationship;
    }

    /**
     * Is the relationship plural
     *
     * @param string $relationship
     * @return bool
     */

    public function isPluralRelationship(string $relationship) :bool
    {
        return ! $this->isSingularRelationship($relationship);
    }
}
