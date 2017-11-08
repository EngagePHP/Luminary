<?php

namespace Luminary\Services\Users;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Luminary\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Spatie\Permission\Traits\HasRoles;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authorizable;
    use Authenticatable;
    use HasRoles;

    /**
     * The permissions guard name
     *
     * @var string
     */
    protected $guard_name = 'api';
}
