<?php

namespace Luminary\Services\ApiRequest\Middleware;

use Closure;
use Luminary\Services\ApiRequest\ApiRequest;
use Luminary\Services\ApiRequest\Content\Content;
use Luminary\Services\ApiRequest\Content\Related;
use Luminary\Services\ApiRequest\Content\Relationship;
use Luminary\Services\ApiRequest\Validation\DeleteRelationship;
use Luminary\Services\ApiRequest\Validation\Patch;
use Luminary\Services\ApiRequest\Validation\PatchRelationship;
use Luminary\Services\ApiRequest\Validation\Post;
use Luminary\Services\ApiRequest\Validation\PostRelationship;

class RequestRouteMiddleware
{
    /**
     * The route request
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The request route
     *
     * @var array
     */
    protected $route;

    /**
     * @var bool
     */
    protected $related = false;

    /**
     * @var bool
     */
    protected $relationship = true;

    /**
     * Run the request filter.
     *
     * @param  \Luminary\Services\ApiRequest\ApiRequest $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(ApiRequest $request, Closure $next)
    {
        $this->request = $request;
        $this->route = $request->route();
        $this->related = (bool) $this->param('related');
        $this->relationship = (bool) $this->param('relationship');

        $method = $request->method();

        switch ($method) {
            case 'POST':
                $this->handlePost($request);
                break;
            case 'PATCH':
                $this->handlePatch($request);
                break;
            case 'DELETE':
                $this->handleDelete($request);
                break;
        }

        return $next($request);
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
        return  end($this->route);
    }

    /**
     * Handle a DELETE Request
     *
     * @param ApiRequest $request
     */
    protected function handleDelete(ApiRequest $request)
    {
        if (! $this->relationship) {
            return;
        }

        (new DeleteRelationship)->validate($request);

        $content = $this->content($request);

        $request->setType($content->type());
        $request->setData($content->attributes());
        $request->setRelationships($content->relationships());
    }

    /**
     * Handle a PATCH Request
     *
     * @param ApiRequest $request
     */
    protected function handlePatch(ApiRequest $request)
    {
        $this->relationship ? (new PatchRelationship)->validate($request) : (new Patch)->validate($request);

        $content = $this->content($request);

        $request->setType($content->type());
        $request->setData($content->attributes());
        $request->setRelationships($content->relationships());
    }

    /**
     * Handle a POST request
     *
     * @param ApiRequest $request
     */
    protected function handlePost(ApiRequest $request)
    {
        $this->relationship ? (new PostRelationship)->validate($request) : (new Post)->validate($request);

        $content = $this->content($request);

        $request->setType($content->type());
        $request->setData($content->attributes());
        $request->setRelationships($content->relationships());
    }

    /**
     * Get the content based on request
     *
     * @param ApiRequest $request
     * @return Content|Related|Relationship
     */
    protected function content(ApiRequest $request)
    {
        $content = $request->json()->all();

        switch (true) {
            case $this->relationship:
                return new Relationship($content, $this->param('relationship'));
                break;
            case $this->related:
                return new Related($content, $this->param('related'));
                break;
            default:
                return new Content($content);
        }
    }
}
