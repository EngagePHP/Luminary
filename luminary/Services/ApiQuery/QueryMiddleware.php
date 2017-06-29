<?php

namespace Luminary\Services\ApiQuery;

use Closure;
use \Luminary\Services\ApiQuery\Query as ApiQuery;

class QueryMiddleware
{

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('GET')) {
            $query = $request->all();

            app(ApiQuery::class)->activate()->setQuery($query);
        }

        // Pass down the response
        return $next($request);
    }
}
