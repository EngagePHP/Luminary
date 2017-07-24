<?php

namespace Luminary\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Luminary\Services\ApiQuery\QueryTrait;
use Luminary\Services\ApiResponse\ResponseModelTrait;
use Luminary\Services\Timezone\TimezoneModelTrait;

class Model extends EloquentModel
{
    use QueryTrait;
    use ResponseModelTrait;
    use TimezoneModelTrait;
}
