<?php

namespace Luminary\Services\ApiResponse\Serializers;

use Illuminate\Database\Eloquent\Model;
use Luminary\Services\ApiResponse\Presenters\RelationshipResponsePresenter;
use Luminary\Services\ApiResponse\ResponseHelper;

class ModelRelationshipSerializer extends ModelSerializer
{
    /**
     * The Request Instance
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The self link
     *
     * @var string
     */
    protected $selfLink;

    /**
     * The related link
     *
     * @var string
     */
    protected $relatedLink;

    /**
     * CollectionSerializer constructor
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Model $data, $request = null)
    {
        $this->request = $request;
        is_null($request) ?: $this->setLinks();

        parent::__construct($data);
    }

    /**
     * Return the data array
     *
     * @return array
     */
    public function data() :array
    {
        return [
            'type' => $this->type(),
            'id' => $this->id()
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
        $this->setType($data->getTable())->setId($data->id);
    }

    /**
     * Return the resource self link
     *
     * @return string
     */
    public function selfLink() :string
    {
        return $this->selfLink;
    }

    /**
     * Return the resource related link
     *
     * @return string
     */
    public function relatedLink() :string
    {
        return $this->relatedLink;
    }

    /**
     * Return the serialized array
     *
     * @return array
     */
    public function serialize() :array
    {
        return (new RelationshipResponsePresenter($this))->format();
    }

    /**
     * Set the relationship links
     *
     * @return void
     */
    protected function setLinks() :void
    {
        $resourceId = $this->param('id');
        $relationship = $this->param('relationship');
        $resource = $this->resource();

        $this->selfLink = ResponseHelper::generateUrl([$resource, $resourceId, 'relationships', $relationship]);
        $this->relatedLink = ResponseHelper::generateUrl([$resource, $resourceId, $relationship]);
    }

    /**
     * Get a route parameter by key
     *
     * @param null $key
     * @return mixed
     */
    protected function param($key = null)
    {
        return array_get($this->params(), $key);
    }

    /**
     * Get all route parameters
     *
     * @return mixed
     */
    protected function params()
    {
        $route = $this->request->route() ?: [];
        return  end($route);
    }
}
