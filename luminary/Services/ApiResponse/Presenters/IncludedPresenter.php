<?php

namespace Luminary\Services\ApiResponse\Presenters;

use Illuminate\Support\Collection;
use Luminary\Services\ApiResponse\Serializers\ModelSerializer;

class IncludedPresenter
{
    /**
     * The model collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $models;

    /**
     * PresenterInterface constructor
     *
     * @param array $models
     */
    public function __construct(array $models)
    {
        $this->models = $models;
    }

    /**
     * Return the formatted presenter array
     *
     * @return array
     */
    public function format() :array
    {
        return collect($this->models)->map(
            function ($model) {
                return $this->formatModel($model);
            }
        )->all();
    }

    /**
     * Format the model as an array
     *
     * @param \Luminary\Services\ApiResponse\Serializers\ModelSerializer $model
     * @return array
     */
    public function formatModel(ModelSerializer $model)
    {
        return $model->data();
    }
}
