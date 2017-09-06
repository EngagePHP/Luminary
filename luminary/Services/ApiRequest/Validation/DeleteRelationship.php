<?php

namespace Luminary\Services\ApiRequest\Validation;

use Luminary\Services\ApiRequest\Traits;

class DeleteRelationship
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
        $this->dataAttributeExists($request->all());
    }
}
