<?php

namespace Luminary\Services\ApiRequest\Validation;

use Luminary\Services\ApiRequest\Traits;

class Post
{
    use Traits\RequiresDataAttribute;
    use Traits\RequiresTypeAttribute;
    use Traits\AcceptsOnlyAttributes;
    use Traits\HasForbiddenAttribute;

    /**
     * Run the request validator.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function validate($request) :void
    {
        $data = array_get($request->all(), 'data');

        $this->dataAttributeExists($request->all());
        $this->typeAttributeExists($data);
        $this->hasAcceptedAttributes($data, ['type', 'attributes', 'relationships']);
        $this->hasForbiddenAttribute('id', $data);
    }
}
