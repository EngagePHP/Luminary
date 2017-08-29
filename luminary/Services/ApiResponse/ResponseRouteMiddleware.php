<?php

namespace Luminary\Services\ApiResponse;

use Closure;

class ResponseRouteMiddleware
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
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->request = $request;
        $this->route = $request->route();

        if ($this->param('relationship')) {
            ResponseMiddleware::$relationshipResponse = true;
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
}
