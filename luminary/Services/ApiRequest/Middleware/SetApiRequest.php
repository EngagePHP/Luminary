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

    /**
     * An empty method for customizing the
     * injected request before being returned
     *
     * @param Request $request
     */
    protected function customizeRequest(Request $request)
    {
        $json = $request->json()->all();

        $request->setAttributesFromContent($json);
        $request->setRelationshipsFromContent($json);
        $request->setTypeFromContent($json);
    }
}
