<?php

namespace Luminary\Services\Testing\Models;

use Luminary\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Luminary\Services\Tenants\ByTenant;

class Interest extends Model
{
    use SoftDeletes;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'interests';

    /**
     * Fields that should stay hidden from response.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot'
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
