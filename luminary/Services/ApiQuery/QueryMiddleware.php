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
            $resource = ['resource' => $request->segment(1) ];

            app(ApiQuery::class)->activate()->setQuery(array_merge($query, $resource));
        }

        // Pass down the response
        return $next($request);
    }
}
