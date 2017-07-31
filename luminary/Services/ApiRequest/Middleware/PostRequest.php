<?php

namespace Luminary\Services\ApiRequest\Middleware;

use Closure;
use Luminary\Services\ApiRequest\Traits;

class PostRequest
{
    use Traits\RequiresDataAttribute;
    use Traits\RequiresTypeAttribute;
    use Traits\AcceptsOnlyAttributes;
    use Traits\HasForbiddenAttribute;

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() === 'POST') {
            $this->validateRequest($request->all());
        }

        return $next($request);
    }

    /**
     * Validate the request input
     *
     * @param array $input
     */
    protected function validateRequest($input)
    {
        $data = array_get($input, 'data');

        $this->dataAttributeExists($input);
        $this->typeAttributeExists($data);
        $this->hasAcceptedAttributes($data, ['type', 'attributes', 'relationships']);
        $this->hasForbiddenAttribute('id', $data);
    }
}
