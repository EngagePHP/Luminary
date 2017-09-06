<?php

namespace Luminary\Services\ApiRequest\Validation;

use Luminary\Services\ApiRequest\Traits;

class Patch
{
    use Traits\RequiresDataAttribute;
    use Traits\RequiresTypeAttribute;
    use Traits\RequiresIdAttribute;
    use Traits\AcceptsOnlyAttributes;

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
        $this->idAttributeExists($data);
        $this->hasAcceptedAttributes($data, ['type', 'id', 'attributes', 'relationships']);
    }
}
