<?php

namespace Luminary\Services\ApiRequest\Middleware;

use Closure;
use Luminary\Services\ApiRequest\Traits;

class PatchRequest
{
    use Traits\RequiresDataAttribute;
    use Traits\RequiresTypeAttribute;
    use Traits\RequiresIdAttribute;
    use Traits\AcceptsOnlyAttributes;

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() === 'PATCH') {
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
        $this->idAttributeExists($data);
        $this->hasAcceptedAttributes($data, ['type', 'id', 'attributes', 'relationships']);
    }
}
