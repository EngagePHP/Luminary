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
        return $this->relationships->map(
            function ($models, $relationship) {
                $models = !empty($models) ? $this->formatModels($models) : $this->formatEmptyModels();
                $links = $this->formatLinks($relationship);

                if (str_singular($relationship) === $relationship && count($models) <= 1) {
                    $models = empty($models) ? null : head($models);
                }

                return [
                    'links' => $links,
                    'data' => $models
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
        $plural = $relationship == str_plural($relationship);

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
     * @return array
     */
    public function formatModels(Collection $models) :array
    {
        return $models->map(
            function (ModelSerializer $model) {
                return $this->formatModel($model);
            }
        )->all();
    }

    /**
     * Return an empty model data attribute
     *
     * @return array
     */
    public function formatEmptyModels() :array
    {
        return [];
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
}
