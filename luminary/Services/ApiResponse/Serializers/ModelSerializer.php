<?php

namespace Luminary\Services\ApiResponse\Serializers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Luminary\Services\ApiResponse\Presenters\IncludedPresenter;
use Luminary\Services\ApiResponse\Presenters\RelationshipPresenter;
use Luminary\Services\ApiResponse\Presenters\ResponsePresenter;
use Luminary\Services\ApiResponse\ResponseHelper;

class ModelSerializer extends AbstractSerializer
{
    /**
     * The model attributes property
     *
     * @var array
     */
    protected $attributes;

    /**
     * The model id
     *
     * @var string
     */
    protected $id;

    /**
     * The model instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The Collection of Model relations
     *
     * @var \Illuminate\Support\Collection
     */
    protected $relations;

    /**
     * The Model relationships array
     *
     * @var array
     */
    protected $relationships;

    /**
     * The model type
     *
     * @var string
     */
    protected $type;

    /**
     * CollectionSerializer constructor
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     */
    public function __construct(Model $data)
    {
        $this->model = $data;

        parent::__construct($data);
    }

    /**
     * Return the data array
     *
     * @return array
     */
    public function data()
    {
        return [
            'type' => $this->type(),
            'id' => $this->id(),
            'attributes' => $this->attributes(),
            'links' => $this->links(),
            'relationships' => $this->relationships(),
            'meta' => $this->meta()
        ];
    }

    /**
     * Parse the model and fill
     * the class attributes
     *
     * @param \Luminary\Database\Eloquent\Model $data
     */
    public function fill($data) :void
    {
        $this->setType($data->getType())
            ->setId($data->id)
            ->setAttributes($data->attributesToArray())
            ->setMeta($data->meta())
            ->setRelations($data->getRelations())
            ->setRelationships($this->relations())
            ->setIncluded($this->flattenedRelations(true));
    }

    /**
     * Return the resource self link
     *
     * @return string
     */
    public function selfLink() :string
    {
        return ResponseHelper::resourceSelf($this->id(), $this->type());
    }

    /**
     * Get the id property
     *
     * @return string
     */
    public function id()
    {
        return  $this->id;
    }

    /**
     * Set the id property
     *
     * @param $id
     * @return ModelSerializer
     */
    public function setId($id) :ModelSerializer
    {
        $this->id = (string) $id;

        return $this;
    }

    /**
     * Get the attributes property
     *
     * @return array
     */
    public function attributes() :array
    {
        return $this->attributes ?: [];
    }

    /**
     * Set the attributes property
     *
     * @param array $attributes
     * @return \Luminary\Services\ApiResponse\Serializers\ModelSerializer
     */
    public function setAttributes(array $attributes) :ModelSerializer
    {
        $attributes = array_except($attributes, ['id', 'type']);

        // Convert all integers larger than 16 to a string
        $this->attributes = collect($attributes)->transform(function($item) {
            return is_int($item) ? (string) $item : $item;
        })->all();

        return $this;
    }

    /**
     * Set/Replaced the included property
     *
     * @param array $included
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setIncluded(array $included) :AbstractSerializer
    {
        $included = (new IncludedPresenter($included))->format();

        parent::setIncluded($included);

        return $this;
    }

    /**
     * Get the relationships property
     *
     * @return array
     */
    public function relationships() :array
    {
        return $this->relationships ?: [];
    }

    /**
     * Set the Relatioinships property
     *
     * @param Collection $models
     * @return \Luminary\Services\ApiResponse\Serializers\ModelSerializer
     */
    public function setRelationships(Collection $models) :ModelSerializer
    {
        $this->relationships = (new RelationshipPresenter($this, $models))->format();

        return $this;
    }

    /**
     * Return the relations property
     *
     * @return \Illuminate\Support\Collection
     */
    public function relations() :Collection
    {
        return $this->relations ?: collect();
    }

    /**
     * Flatten the relations collection
     *
     * @param bool $nested
     * @return array
     */
    public function flattenedRelations($nested = false) :array
    {
        $relations = $this->relations()->flatten();

        return $nested
            ? $this->includeNestedRelations($relations)->all()
            : $relations->all();
    }

    /**
     * Set the included and relationships properties
     *
     * @param array $relations
     * @return \Luminary\Services\ApiResponse\Serializers\ModelSerializer
     */
    public function setRelations(array $relations) :ModelSerializer
    {
        $this->relations = collect($relations)->filter(
            function ($model) {
                return (! $model instanceof Pivot);
            }
        )->map(
            function ($models) {
                $collection = $this->modelsToCollection($models);
                return $this->serializeModels($collection);
            }
        );

        return $this;
    }

    /**
     * Return the resource related link
     *
     * @return string
     */
    public function relatedLink() :string
    {
        return '';
    }

    /**
     * Return the serialized array
     *
     * @return array
     */
    public function serialize() :array
    {
        return (new ResponsePresenter($this))->format();
    }

    /**
     * Return a collection where each models as
     * a ModelSerializer instance
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     * @return \Illuminate\Support\Collection
     */
    public function serializeModels(EloquentCollection $models) :Collection
    {
        return $models->map(
            function (Model $model) {
                return new ModelSerializer($model);
            }
        );
    }

    /**
     * Get the type property
     *
     * @return string
     */
    public function type() :string
    {
        return (string) $this->type;
    }

    /**
     * Set the type property
     *
     * @param string $type
     * @return \Luminary\Services\ApiResponse\Serializers\ModelSerializer
     */
    public function setType(string $type) :ModelSerializer
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Create a collection from
     * mixed model results
     *
     * @param mixed $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function modelsToCollection($models) :EloquentCollection
    {
        switch (true) {
            case is_null($models):
                return new EloquentCollection;
                break;
            case $models instanceof Model:
                return $models->newCollection([$models]);
                break;
            default:
                return $models;
        }
    }

    /**
     * Include the models nested relationships
     *
     * @param Collection $relations
     * @return Collection
     */
    protected function includeNestedRelations(Collection $relations) :Collection
    {
        $models = $relations->map(function ($model) {
            return $model->flattenedRelations(true);
        })->filter(function ($relation) {
            return ! empty($relation);
        })->flatten();

        return $relations->merge($models);
    }
}
