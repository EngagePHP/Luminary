<?php

namespace Luminary\Services\Testing\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Luminary\Services\Tenants\ByTenant;
use Luminary\Services\Users\User as LuminaryUser;

class User extends LuminaryUser
{
    use ByTenant;
    use SoftDeletes;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Fillable fields for a Content instance.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Fields that should stay hidden from response.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The customer relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * The location relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
