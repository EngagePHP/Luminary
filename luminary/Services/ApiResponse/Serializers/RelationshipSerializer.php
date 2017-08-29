<?php

namespace Luminary\Services\ApiResponse\Serializers;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Luminary\Services\ApiQuery\Pagination\Collection as PaginatorCollection;
use Luminary\Services\ApiResponse\Presenters\LinkPresenter;
use Luminary\Services\ApiResponse\ResponseHelper;

abstract class RelationshipSerializer extends AbstractSerializer
{
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

    protected function setLinks()
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
