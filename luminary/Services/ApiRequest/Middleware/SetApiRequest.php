<?php

namespace Luminary\Services\ApiRequest\Middleware;

use Closure;
use Luminary\Services\ApiRequest\Traits\HasCustomRequest;
use Luminary\Services\ApiRequest\ApiRequest as Request;

class SetApiRequest
{
    use HasCustomRequest;

    /**
     * Inject the API Request Class.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request = $this->injectRequest(Request::class, $request);

        return $next($request);
    }
}
