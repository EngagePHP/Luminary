<?php

namespace  Luminary\EventHooks;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventHookMiddleware
{
    /**
     * Run the hook middleware
     *
     * @param  Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $event = Event::find($request->get('event'))) {
            throw new NotFoundHttpException('The event requested does not exist');
        }

        $request->merge(compact('event'));

        return $next($request);
    }
}
